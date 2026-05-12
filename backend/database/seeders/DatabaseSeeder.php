<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hobby;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@hobispace.lv'],
            [
                'name' => 'Administrators',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
        );

        $categories = [
            'Sports' => 'Aktivitates, kas uzlabo fizisko formu un izturibu.',
            'Radosie hobiji' => 'Nodarbes, kuras palidz izpaust idejas un radosumu.',
            'Muzika' => 'Dziedasana, instrumenti un citas muzikalas aktivitates.',
            'Daba' => 'Hobiji, kas saistiti ar uzturesanos ara un dabas izpeti.',
            'Majai' => 'Praktiski hobiji majas videi, receptem un rokdarbiem.',
            'Digitalie hobiji' => 'Aktivitates, kas saistitas ar tehnologijam un spelem.',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        $hobbies = [
            ['Fotografesana', 'Radosie hobiji', 'Mirkla iemuzinasana fotografijas.', 'videjs', 'videjas'],
            ['Skriesana', 'Sports', 'Aktivs hobijs veseliba, izturibai un disciplinai.', 'viegls', 'bezmaksas'],
            ['Dejosana', 'Sports', 'Kustiba muzikas ritma un pasparliecibas attistisana.', 'videjs', 'zemas'],
            ['Dziedasana', 'Muzika', 'Balss trenins, dziesmas un muzikalas izpausmes.', 'videjs', 'bezmaksas'],
            ['Susana', 'Majai', 'Auduma darbu, apgerbu un praktisku projektu veidosana.', 'sarezgits', 'videjas'],
            ['Lasisana', 'Radosie hobiji', 'Gramatu un stastu pasaules iepazisana.', 'viegls', 'zemas'],
            ['Fitness', 'Sports', 'Trenini spekam, kustibai un labsajutai.', 'videjs', 'videjas'],
            ['Gleznosana', 'Radosie hobiji', 'Radosa izpausme ar krasam uz papira vai audekla.', 'videjs', 'videjas'],
            ['Rakstisana', 'Radosie hobiji', 'Domu, stastu un ideju pierakstisana.', 'viegls', 'bezmaksas'],
            ['Ritenbrauksana', 'Sports', 'Marsrutu izpete un fiziska aktivitate ara.', 'videjs', 'augstas'],
            ['Peldesana', 'Sports', 'Sports un atputa udeni.', 'videjs', 'videjas'],
            ['Muzika', 'Muzika', 'Instrumentu spele vai muzikas veidosana.', 'sarezgits', 'videjas'],
            ['Makskeresana', 'Daba', 'Mieriga atputa pie udens un pacietibas trenins.', 'videjs', 'videjas'],
            ['Videospele', 'Digitalie hobiji', 'Digitala izklaide, strategija un sadarbiba.', 'viegls', 'videjas'],
            ['Ediena gatavosana', 'Majai', 'Recepsu izmeginajumi un jaunu garsu atklasana.', 'videjs', 'videjas'],
            ['Cepsana', 'Majai', 'Kuku, cepumu un citu gardumu gatavosana.', 'videjs', 'videjas'],
            ['Rokdarbi', 'Majai', 'Praktisku un dekorativu lietu veidosana ar rokam.', 'viegls', 'zemas'],
        ];

        foreach ($hobbies as [$name, $categoryName, $description, $difficulty, $estimatedCost]) {
            Hobby::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => Category::where('name', $categoryName)->value('id'),
                    'user_id' => $admin->id,
                    'description' => $description,
                    'difficulty' => $difficulty,
                    'estimated_cost' => $estimatedCost,
                    'is_public' => true,
                ],
            );
        }
    }
}
