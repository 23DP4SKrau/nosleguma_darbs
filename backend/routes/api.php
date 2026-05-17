<?php

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Hobby;
use App\Models\HobbyLog;
use App\Models\User;
use App\Models\UserQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => 'user',
    ]);

    return response()->json([
        'message' => 'Konts veiksmigi izveidots!',
        'user' => $user,
    ], 201);
});

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required', 'string'],
        'password' => ['required'],
    ]);

    if ($data['email'] === 'admin') {
        if ($data['password'] !== 'admin1234567') {
            throw ValidationException::withMessages([
                'email' => ['Nepareizs admin lietotājs vai parole.'],
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => 'admin@hobispace.lv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin1234567'),
                'role' => 'admin',
            ],
        );

        return response()->json([
            'message' => 'Admin pieslēgšanās veiksmīga!',
            'user' => $user,
        ]);
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw ValidationException::withMessages([
            'email' => ['Ievadi derīgu e-pasta adresi.'],
        ]);
    }

    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Nepareizs e-pasts vai parole.'],
        ]);
    }

    return response()->json([
        'message' => 'Pieslegsanas veiksmiga!',
        'user' => $user,
    ]);
});

$requireAdmin = function (Request $request) {
    $adminId = $request->input('admin_id') ?? $request->query('admin_id');
    $admin = User::find($adminId);

    if (!$admin || $admin->role !== 'admin') {
        abort(403, 'Šī sadaļa pieejama tikai administratoram.');
    }
};

$deleteUserProfile = function (User $user) {
    if ($user->avatar_path) {
        $avatarPath = public_path(ltrim($user->avatar_path, '/'));

        if (is_file($avatarPath)) {
            unlink($avatarPath);
        }
    }

    $user->favoriteHobbies()->detach();
    $user->hobbyLogs()->delete();
    $user->questions()->update(['user_id' => null]);
    Hobby::where('user_id', $user->id)->update(['user_id' => null]);
    $user->delete();
};

Route::get('/admin/users', function (Request $request) use ($requireAdmin) {
    $requireAdmin($request);

    return User::query()
        ->withCount(['favoriteHobbies as favorites_count', 'hobbyLogs as logs_count', 'questions as questions_count'])
        ->orderByDesc('role')
        ->orderBy('name')
        ->get();
});

Route::put('/admin/users/{user}', function (Request $request, User $user) use ($requireAdmin) {
    $requireAdmin($request);

    $data = $request->validate([
        'admin_id' => ['required', 'integer'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        'role' => ['required', Rule::in(['user', 'admin'])],
    ]);

    unset($data['admin_id']);

    $user->update($data);

    return response()->json([
        'message' => 'Lietotāja profils atjaunots!',
        'user' => $user,
    ]);
});

Route::delete('/admin/users/{user}', function (Request $request, User $user) use ($requireAdmin, $deleteUserProfile) {
    $requireAdmin($request);

    if ((int) $request->input('admin_id') === $user->id) {
        abort(422, 'Admins nevar izdzēst pats savu profilu no šī saraksta.');
    }

    $deleteUserProfile($user);

    return response()->json([
        'message' => 'Lietotāja profils ir izdzēsts.',
    ]);
});

Route::get('/users/{user}/profile', function (User $user) {
    return response()->json([
        'user' => $user,
        'stats' => [
            'favorites_count' => $user->favoriteHobbies()->count(),
            'logs_count' => $user->hobbyLogs()->count(),
            'questions_count' => $user->questions()->count(),
        ],
        'recent_logs' => $user->hobbyLogs()
            ->with('hobby:id,name,category_id')
            ->orderByDesc('log_date')
            ->limit(5)
            ->get(),
    ]);
});

Route::put('/users/{user}', function (Request $request, User $user) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
    ]);

    $user->update($data);

    return response()->json([
        'message' => 'Profils veiksmigi atjaunots!',
        'user' => $user,
    ]);
});

Route::post('/users/{user}/avatar', function (Request $request, User $user) {
    $data = $request->validate([
        'avatar' => ['required', 'image', 'max:4096'],
    ]);

    $file = $data['avatar'];
    $fileName = uniqid('avatar_', true).'.'.$file->getClientOriginalExtension();
    $uploadPath = public_path('uploads/avatars');

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $file->move($uploadPath, $fileName);

    $user->update([
        'avatar_path' => '/uploads/avatars/'.$fileName,
    ]);

    return response()->json([
        'message' => 'Profila bilde saglabata!',
        'user' => $user,
    ]);
});

Route::delete('/users/{user}/avatar', function (User $user) {
    if ($user->avatar_path) {
        $avatarPath = public_path(ltrim($user->avatar_path, '/'));

        if (is_file($avatarPath)) {
            unlink($avatarPath);
        }
    }

    $user->update([
        'avatar_path' => null,
    ]);

    return response()->json([
        'message' => 'Profila bilde nonemta!',
        'user' => $user,
    ]);
});

Route::delete('/users/{user}', function (Request $request, User $user) use ($deleteUserProfile) {
    $data = $request->validate([
        'password' => ['required', 'string'],
        'confirmation' => ['required', 'string', Rule::in(['IZDZESTPROFILU'])],
    ]);

    if (!Hash::check($data['password'], $user->password)) {
        throw ValidationException::withMessages([
            'password' => ['Parole nav pareiza.'],
        ]);
    }

    $deleteUserProfile($user);

    return response()->json([
        'message' => 'Profils ir pilniba izdzests.',
    ]);
});

Route::get('/categories', function () {
    return Category::query()
        ->withCount('hobbies')
        ->orderBy('name')
        ->get();
});

Route::get('/hobbies', function (Request $request) {
    $sortBy = $request->query('sort_by', 'name');
    $sortDirection = $request->query('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
    $allowedSorts = ['name', 'difficulty', 'estimated_cost', 'created_at'];

    $query = Hobby::query()
        ->with(['category:id,name', 'user:id,name'])
        ->withCount(['logs', 'favoredByUsers as favorites_count'])
        ->where('is_public', true);

    if ($request->filled('search')) {
        $search = $request->query('search');

        $query->where(function ($innerQuery) use ($search) {
            $innerQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('category', function ($categoryQuery) use ($search) {
                    $categoryQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->integer('category_id'));
    }

    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->query('difficulty'));
    }

    if ($request->filled('estimated_cost')) {
        $query->where('estimated_cost', $request->query('estimated_cost'));
    }

    return $query
        ->orderBy(in_array($sortBy, $allowedSorts, true) ? $sortBy : 'name', $sortDirection)
        ->get();
});

Route::post('/hobbies', function (Request $request) {
    $data = $request->validate([
        'category_id' => ['required', 'exists:categories,id'],
        'user_id' => ['nullable', 'exists:users,id'],
        'name' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'min:10'],
        'image' => ['nullable', 'string', 'max:255'],
        'image_file' => ['nullable', 'image', 'max:4096'],
        'difficulty' => ['nullable', Rule::in(['viegls', 'videjs', 'sarezgits'])],
        'estimated_cost' => ['required', Rule::in(['bezmaksas', 'zemas', 'videjas', 'augstas'])],
        'custom_log_label' => ['nullable', 'string', 'max:255'],
    ]);

    $data['difficulty'] = $data['difficulty'] ?? 'viegls';

    if ($request->hasFile('image_file')) {
        $file = $request->file('image_file');
        $fileName = uniqid('hobby_', true).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/hobbies'), $fileName);
        $data['image'] = '/uploads/hobbies/'.$fileName;
    }

    unset($data['image_file']);

    $hobby = Hobby::create($data)->load(['category:id,name', 'user:id,name']);

    return response()->json([
        'message' => 'Hobijs veiksmigi pievienots!',
        'hobby' => $hobby,
    ], 201);
});

Route::put('/hobbies/{hobby}', function (Request $request, Hobby $hobby) {
    $data = $request->validate([
        'category_id' => ['required', 'exists:categories,id'],
        'name' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'min:10'],
        'image' => ['nullable', 'string', 'max:255'],
        'difficulty' => ['required', Rule::in(['viegls', 'videjs', 'sarezgits'])],
        'estimated_cost' => ['required', Rule::in(['bezmaksas', 'zemas', 'videjas', 'augstas'])],
        'custom_log_label' => ['nullable', 'string', 'max:255'],
        'is_public' => ['boolean'],
    ]);

    $hobby->update($data);

    return response()->json([
        'message' => 'Hobijs veiksmigi atjaunots!',
        'hobby' => $hobby->load(['category:id,name', 'user:id,name']),
    ]);
});

Route::delete('/hobbies/{hobby}', function (Hobby $hobby) {
    $hobby->delete();

    return response()->json([
        'message' => 'Hobijs veiksmigi dzests!',
    ]);
});

Route::get('/hobby-logs', function (Request $request) {
    return HobbyLog::query()
        ->with(['hobby:id,name,category_id', 'hobby.category:id,name', 'user:id,name'])
        ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
        ->when($request->filled('hobby_id'), fn ($query) => $query->where('hobby_id', $request->integer('hobby_id')))
        ->orderByDesc('log_date')
        ->get();
});

Route::post('/hobby-logs', function (Request $request) {
    $data = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'hobby_id' => ['required', 'exists:hobbies,id'],
        'log_date' => ['required', 'date', 'before_or_equal:today'],
        'title' => ['required', 'string', 'max:255'],
        'notes' => ['nullable', 'string'],
        'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        'specific_value' => ['nullable', 'string', 'max:255'],
        'mood_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
    ]);

    $log = HobbyLog::create($data)->load(['hobby:id,name', 'user:id,name']);

    return response()->json([
        'message' => 'Ieraksts veiksmigi saglabats!',
        'log' => $log,
    ], 201);
});

Route::put('/hobby-logs/{hobbyLog}', function (Request $request, HobbyLog $hobbyLog) {
    $data = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'hobby_id' => ['required', 'exists:hobbies,id'],
        'log_date' => ['required', 'date', 'before_or_equal:today'],
        'title' => ['required', 'string', 'max:255'],
        'notes' => ['nullable', 'string'],
        'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        'specific_value' => ['nullable', 'string', 'max:255'],
        'mood_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
    ]);

    $hobbyLog->update($data);

    return response()->json([
        'message' => 'Ieraksts veiksmigi atjaunots!',
        'log' => $hobbyLog->load(['hobby:id,name', 'user:id,name']),
    ]);
});

Route::delete('/hobby-logs/{hobbyLog}', function (HobbyLog $hobbyLog) {
    $hobbyLog->delete();

    return response()->json([
        'message' => 'Ieraksts veiksmigi dzests!',
    ]);
});

Route::post('/favorites/toggle', function (Request $request) {
    $data = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'hobby_id' => ['required', 'exists:hobbies,id'],
    ]);

    $favorite = Favorite::where($data)->first();

    if ($favorite) {
        $favorite->delete();

        return response()->json([
            'message' => 'Hobijs nonemts no favoritiem.',
            'is_favorite' => false,
        ]);
    }

    Favorite::create($data);

    return response()->json([
        'message' => 'Hobijs pievienots favoritiem.',
        'is_favorite' => true,
    ], 201);
});

Route::post('/favorites', function (Request $request) {
    $data = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'hobby_id' => ['required', 'exists:hobbies,id'],
    ]);

    Favorite::firstOrCreate($data);

    return response()->json([
        'message' => 'Hobijs pievienots favoritiem.',
        'is_favorite' => true,
    ], 201);
});

Route::get('/favorites/{user}', function (User $user) {
    return $user->favoriteHobbies()
        ->with('category:id,name')
        ->withCount('logs')
        ->orderBy('name')
        ->get();
});

Route::post('/user-questions', function (Request $request) {
    $data = $request->validate([
        'user_id' => ['nullable', 'exists:users,id'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'subject' => ['required', 'string', 'max:255'],
        'message' => ['required', 'string', 'min:10'],
    ]);

    $question = UserQuestion::create($data);

    return response()->json([
        'message' => 'Jautājums veiksmīgi saglabāts!',
        'question' => $question,
    ], 201);
});

Route::get('/user-questions', function () {
    return UserQuestion::query()
        ->with('user:id,name,email')
        ->latest()
        ->get();
});

Route::get('/statistics', function () {
    return response()->json([
        'total_hobbies' => Hobby::count(),
        'total_logs' => HobbyLog::count(),
        'total_favorites' => Favorite::count(),
        'hobbies_by_category' => Category::query()
            ->select('id', 'name')
            ->withCount('hobbies')
            ->orderByDesc('hobbies_count')
            ->get(),
        'popular_hobbies' => Hobby::query()
            ->with('category:id,name')
            ->withCount(['logs', 'favoredByUsers as favorites_count'])
            ->orderByDesc('favorites_count')
            ->limit(5)
            ->get(),
    ]);
});
