<?php

namespace Database\Seeders;

use App\Models\Constituency;
use App\Models\County;
use App\Models\Ward;
use App\Models\Location;
use Illuminate\Database\Seeder;

class CountyConstituencySeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        // County and Constituency data
        $countiesWards = [
            'Mombasa' => ['Changamwe', 'Jomvu', 'Kisauni', 'Nyali', 'Likoni', 'Mvita',],
            'Kwale' => ['Msambweni', 'Matuga', 'Kinango', 'Lunga Lunga',],
            'Kilifi' => ['Malindi', 'Magarini', 'Kilifi North', 'Kilifi South', 'Kaloleni', 'Rabai',],
            'Tana River' => ['Garsen', 'Galole', 'Bura',],
            'Lamu' => ['Lamu East', 'Lamu West',],
            'Taita Taveta' => ['Voi', 'Mwatate', 'Wundanyi', 'Taveta',],
            'Garissa' => ['Garissa Township', 'Balambala', 'Lagdera', 'Dadaab', 'Fafi', 'Ijara',],
            'Wajir' => ['Wajir North', 'Wajir East', 'Tarbaj', 'Wajir West', 'Eldas', 'Wajir South',],
            'Mandera' => ['Mandera West', 'Banissa', 'Mandera North', 'Mandera South', 'Mandera East', 'Lafey',],
            'Marsabit' => ['Moyale', 'North Horr', 'Saku', 'Laisamis',],
            'Isiolo' => ['Isiolo North', 'Isiolo South',],
            'Meru' => ['Imenti South', 'Imenti North', 'Imenti Central', 'Tigania East', 'Tigania West', 'Buuri', 'Igembe South', 'Igembe Central', 'Igembe North',],
            'Tharaka Nithi' => ['Tharaka', 'Chuka/Igambang\'ombe','Maara',],
            'Embu' => ['Manyatta','Runyenjes','Mbeere South','Mbeere North',],
            'Kitui' => ['Kitui Rural','Kitui South','Kitui Central','Kitui East','Kitui West','Mwingi Central','Mwingi North','Mwingi West',],
            'Machakos' => ['Machakos Town','Mavoko','Kathiani','Mwala','Yatta','Kangundo','Matungulu',],
            'Makueni' => ['Makueni','Kibwezi West','Kibwezi East','Kaiti','Kilome','Mbooni',],
            'Nyandarua' => ['Kinangop','Kipipiri','Ol Kalou','Ol Jorok','Ndaragwa',],
            'Nyeri' => ['Tetu','Kieni','Mathira','Othaya','Mukurweini','Nyeri Town',],
            'Kirinyaga' => ['Mwea','Gichugu','Ndia','Kirinyaga Central',],
            'Muranga' => ['Kangema','Mathioya','Kiharu','Kigumo','Maragua','Kandara','Gatanga',],
            'Kiambu' => ['Kiambu','Kiambaa','Kabete','Kikuyu','Limuru','Lari','Githunguri','Juja','Thika Town','Ruiru','Gatundu North','Gatundu South',],
            'Turkana' => ['Turkana North','Turkana West','Turkana Central','Loima','Turkana South','Turkana East',],
            'West Pokot' => ['Kapenguria','Sigor','Kacheliba','Pokot South',],
            'Samburu' => ['Samburu West','Samburu North','Samburu East',],
            'Trans Nzoia' => ['Kwanza','Endebess','Saboti','Kiminini','Cherangany',],
            'Uasin Gishu' => ['Soy','Turbo','Moiben','Ainabkoi','Kapseret','Kesses',],
            'Elgeyo Marakwet' => ['Marakwet East','Marakwet West','Keiyo North','Keiyo South',],
            'Nandi' => ['Tinderet','Aldai','Nandi Hills','Chesumei','Emgwen','Mosop',],
            'Baringo' => ['Tiaty','Baringo North','Baringo Central','Baringo South','Eldama Ravine','Mogotio',],
            'Laikipia' => ['Laikipia West','Laikipia East','Laikipia North',],
            'Nakuru' => ['Naivasha','Nakuru Town West','Nakuru Town East','Subukia','Rongai','Bahati','Gilgil','Kuresoi North','Kuresoi South','Molo','Njoro',],
            'Narok' => ['Kilgoris','Emurua Dikirr','Narok North','Narok East','Narok South','Narok West',],
            'Kajiado' => ['Kajiado North','Kajiado Central','Kajiado East','Kajiado West','Kajiado South',],
            'Kericho' => ['Belgut','Bureti','Ainamoi','Kipkelion East','Kipkelion West','Sigowet/Soin',],
            'Bomet' => ['Sotik','Chepalungu','Bomet East','Bomet Central','Konoin',],
            'Kakamega' => ['Lugari','Likuyani','Malava','Lurambi','Mumias East','Mumias West','Navakholo','Butere','Khwisero','Matungu','Shinyalu','Ikolomani',],
            'Vihiga' => ['Vihiga','Sabatia','Hamisi','Emuhaya','Luanda',],
            'Bungoma' => ['Kabuchai','Bumula','Kanduyi','Webuye East','Webuye West','Sirisia','Mt. Elgon','Tongaren',],
            'Busia' => ['Teso North','Teso South','Nambale','Matayos','Butula','Funyula','Budalangi',],
            'Siaya' => ['Ugenya','Ugunja','Alego Usonga','Gem','Bondo','Rarieda',],
            'Kisumu' => ['Kisumu East','Kisumu West','Kisumu Central','Seme','Nyando','Muhoroni','Nyakach',],
            'Homa Bay' => ['Ndhiwa','Rangwe','Homa Bay Town','Kabondo Kasipul','Kasipul','Suba North','Suba South',],
            'Migori' => ['Rongo','Awendo','Suna East','Suna West','Uriri','Nyatike','Kuria East','Kuria West',],
            'Kisii' => ['Kitutu Chache North','Kitutu Chache South','Nyaribari Masaba','Nyaribari Chache','Bobasi','Bonchari','South Mugirango',],
            'Nyamira' => ['Borabu','North Mugirango','West Mugirango','Kitutu Masaba',],
            'Nairobi' => ['Westlands','Dagoretti North','Dagoretti South','Langata','Kibra','Roysambu','Kasarani','Ruaraka','Embakasi South','Embakasi North','Embakasi Central','Embakasi East','Embakasi West','Makadara','Kamukunji','Starehe','Mathare',],
        ];



        // Loop through each county and create associated constituencies, wards, and locations
        foreach ( $countiesWards as $countyName => $constituencies ) {
            // Create County
            $county = County::create( [ 'name' => $countyName ] );

            // Create Constituencies for the County
            foreach ( $constituencies as $constituencyName ) {
                $constituency = Constituency::create( [
                    'name' => $constituencyName,
                    'county_id' => $county->id,
                ] );

                // Define wards for each constituency
                $wards = $this->getWardsForConstituency( $constituencyName );

                // Insert each ward into the database
                foreach ($wards as $wardName) {
                    $ward = Ward::create([
                        'name' => $wardName,
                        'constituency_id' => $constituency->id,
                        'county_id' => $county->id,
                    ]);
                
                    // Now, get locations for this specific ward
                    $locations = $this->getLocationsForWard($wardName);
                
                    foreach ($locations as $locationName) {
                        Location::create([
                            'name' => $locationName,
                            'ward_id' => $ward->id,
                            'constituency_id' => $constituency->id,
                            'county_id' => $county->id,
                        ]);
                    }
                }
            }
        }

        $this->command->info( 'Counties, Constituencies, Wards, and Locations added successfully.' );
    }

    /**
    * Get wards for a specific constituency.
    */

    private function getWardsForConstituency( $constituencyName ): array {
        $wardsData = [
            'Westlands' => [ 'Karura', 'Kangemi', 'Kitisuru', 'Mountain View', 'Parklands/Highridge' ],
        ];

        return $wardsData[ $constituencyName ] ?? [];
    }

    /**
    * Get locations for a specific ward.
    */

    private function getLocationsForWard( $wardName ): array {
        $locationsData = [
            'Karura' => [
                'Westgate',
            ],
        ];

        return $locationsData[ $wardName ] ?? [];
    }
}