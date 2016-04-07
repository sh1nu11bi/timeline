<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require('vendor/autoload.php');

require 'config.php';

require 'db.php';

function str_replace_first($search, $replace, $subject) 
{
    return implode($replace, explode($search, $subject, 2));
}



class IntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public $number_tests = 1;
    
    public $number_docs = 100;
    
    //$time_delta_docs is the time difference between docs in seconds
    // 86400 is one day
    public $time_delta_docs = 86400;
    
    public $test_spacename = 'EMOJIFY';    

    public $api_url = 'http://172.27.85.169/api';

    public $external_url = 'http://172.27.85.154/api';
    
    public $integration_path = '/integration/';
    
    public $all_searchs = array();
    
    public $all_users = array('udhokale', 'josegarcia', 'gretkowsky', 'kvenable', 'jguies');
    
    public $current_user = '';
    
    public $next_user = '';

    public $type_entries = array('OPEN', 'ACKNOWLEDGED', 'CLOSE');

    public $pause_documents = 0.2;

    public $emoji_list = ':bowtie: :smile: :laughing: :blush: :smiley: :relaxed: :smirk: :heart_eyes: :kissing_heart: :kissing_closed_eyes: :flushed: :relieved: :satisfied: :grin: :wink: :stuck_out_tongue_winking_eye: :stuck_out_tongue_closed_eyes: :grinning: :kissing: :kissing_smiling_eyes: :stuck_out_tongue: :sleeping: :worried: :frowning: :anguished: :open_mouth: :grimacing: :confused: :hushed: :expressionless: :unamused: :sweat_smile: :sweat: :disappointed_relieved: :weary: :pensive: :disappointed: :confounded: :fearful: :cold_sweat: :persevere: :cry: :sob: :joy: :astonished: :scream: :neckbeard: :tired_face: :angry: :rage: :triumph: :sleepy: :yum: :mask: :sunglasses: :dizzy_face: :imp: :smiling_imp: :neutral_face: :no_mouth: :innocent: :alien: :yellow_heart: :blue_heart: :purple_heart: :heart: :green_heart: :broken_heart: :heartbeat: :heartpulse: :two_hearts: :revolving_hearts: :cupid: :sparkling_heart: :sparkles: :star: :star2: :dizzy: :boom: :collision: :anger: :exclamation: :question: :grey_exclamation: :grey_question: :zzz: :dash: :sweat_drops: :notes: :musical_note: :fire: :hankey: :poop: :shit: :+1: :thumbsup: :-1: :thumbsdown: :ok_hand: :punch: :facepunch: :fist: :v: :wave: :hand: :raised_hand: :open_hands: :point_up: :point_down: :point_left: :point_right: :raised_hands: :pray: :point_up_2: :clap: :muscle: :metal: :fu: :runner: :running: :couple: :family: :two_men_holding_hands: :two_women_holding_hands: :dancer: :dancers: :ok_woman: :no_good: :information_desk_person: :raising_hand: :bride_with_veil: :person_with_pouting_face: :person_frowning: :bow: :couplekiss: :couple_with_heart: :massage: :haircut: :nail_care: :boy: :girl: :woman: :man: :baby: :older_woman: :older_man: :person_with_blond_hair: :man_with_gua_pi_mao: :man_with_turban: :construction_worker: :cop: :angel: :princess: :smiley_cat: :smile_cat: :heart_eyes_cat: :kissing_cat: :smirk_cat: :scream_cat: :crying_cat_face: :joy_cat: :pouting_cat: :japanese_ogre: :japanese_goblin: :see_no_evil: :hear_no_evil: :speak_no_evil: :guardsman: :skull: :feet: :lips: :kiss: :droplet: :ear: :eyes: :nose: :tongue: :love_letter: :bust_in_silhouette: :busts_in_silhouette: :speech_balloon: :thought_balloon: :feelsgood: :finnadie: :goberserk: :godmode: :hurtrealbad: :rage1: :rage2: :rage3: :rage4: :suspect: :trollface: Nature

:sunny: :umbrella: :cloud: :snowflake: :snowman: :zap: :cyclone: :foggy: :ocean: :cat: :dog: :mouse: :hamster: :rabbit: :wolf: :frog: :tiger: :koala: :bear: :pig: :pig_nose: :cow: :boar: :monkey_face: :monkey: :horse: :racehorse: :camel: :sheep: :elephant: :panda_face: :snake: :bird: :baby_chick: :hatched_chick: :hatching_chick: :chicken: :penguin: :turtle: :bug: :honeybee: :ant: :beetle: :snail: :octopus: :tropical_fish: :fish: :whale: :whale2: :dolphin: :cow2: :ram: :rat: :water_buffalo: :tiger2: :rabbit2: :dragon: :goat: :rooster: :dog2: :pig2: :mouse2: :ox: :dragon_face: :blowfish: :crocodile: :dromedary_camel: :leopard: :cat2: :poodle: :paw_prints: :bouquet: :cherry_blossom: :tulip: :four_leaf_clover: :rose: :sunflower: :hibiscus: :maple_leaf: :leaves: :fallen_leaf: :herb: :mushroom: :cactus: :palm_tree: :evergreen_tree: :deciduous_tree: :chestnut: :seedling: :blossom: :ear_of_rice: :shell: :globe_with_meridians: :sun_with_face: :full_moon_with_face: :new_moon_with_face: :new_moon: :waxing_crescent_moon: :first_quarter_moon: :waxing_gibbous_moon: :full_moon: :waning_gibbous_moon: :last_quarter_moon: :waning_crescent_moon: :last_quarter_moon_with_face: :first_quarter_moon_with_face: :crescent_moon: :earth_africa: :earth_americas: :earth_asia: :volcano: :milky_way: :partly_sunny: :octocat: :squirrel: Objects

:bamboo: :gift_heart: :dolls: :school_satchel: :mortar_board: :flags: :fireworks: :sparkler: :wind_chime: :rice_scene: :jack_o_lantern: :ghost: :santa: :christmas_tree: :gift: :bell: :no_bell: :tanabata_tree: :tada: :confetti_ball: :balloon: :crystal_ball: :cd: :dvd: :floppy_disk: :camera: :video_camera: :movie_camera: :computer: :tv: :iphone: :phone: :telephone: :telephone_receiver: :pager: :fax: :minidisc: :vhs: :sound: :speaker: :mute: :loudspeaker: :mega: :hourglass: :hourglass_flowing_sand: :alarm_clock: :watch: :radio: :satellite: :loop: :mag: :mag_right: :unlock: :lock: :lock_with_ink_pen: :closed_lock_with_key: :key: :bulb: :flashlight: :high_brightness: :low_brightness: :electric_plug: :battery: :calling: :email: :mailbox: :postbox: :bath: :bathtub: :shower: :toilet: :wrench: :nut_and_bolt: :hammer: :seat: :moneybag: :yen: :dollar: :pound: :euro: :credit_card: :money_with_wings: :e-mail: :inbox_tray: :outbox_tray: :envelope: :incoming_envelope: :postal_horn: :mailbox_closed: :mailbox_with_mail: :mailbox_with_no_mail: :package: :door: :smoking: :bomb: :gun: :hocho: :pill: :syringe: :page_facing_up: :page_with_curl: :bookmark_tabs: :bar_chart: :chart_with_upwards_trend: :chart_with_downwards_trend: :scroll: :clipboard: :calendar: :date: :card_index: :file_folder: :open_file_folder: :scissors: :pushpin: :paperclip: :black_nib: :pencil2: :straight_ruler: :triangular_ruler: :closed_book: :green_book: :blue_book: :orange_book: :notebook: :notebook_with_decorative_cover: :ledger: :books: :bookmark: :name_badge: :microscope: :telescope: :newspaper: :football: :basketball: :soccer: :baseball: :tennis: :8ball: :rugby_football: :bowling: :golf: :mountain_bicyclist: :bicyclist: :horse_racing: :snowboarder: :swimmer: :surfer: :ski: :spades: :hearts: :clubs: :diamonds: :gem: :ring: :trophy: :musical_score: :musical_keyboard: :violin: :space_invader: :video_game: :black_joker: :flower_playing_cards: :game_die: :dart: :mahjong: :clapper: :memo: :pencil: :book: :art: :microphone: :headphones: :trumpet: :saxophone: :guitar: :shoe: :sandal: :high_heel: :lipstick: :boot: :shirt: :tshirt: :necktie: :womans_clothes: :dress: :running_shirt_with_sash: :jeans: :kimono: :bikini: :ribbon: :tophat: :crown: :womans_hat: :mans_shoe: :closed_umbrella: :briefcase: :handbag: :pouch: :purse: :eyeglasses: :fishing_pole_and_fish: :coffee: :tea: :sake: :baby_bottle: :beer: :beers: :cocktail: :tropical_drink: :wine_glass: :fork_and_knife: :pizza: :hamburger: :fries: :poultry_leg: :meat_on_bone: :spaghetti: :curry: :fried_shrimp: :bento: :sushi: :fish_cake: :rice_ball: :rice_cracker: :rice: :ramen: :stew: :oden: :dango: :egg: :bread: :doughnut: :custard: :icecream: :ice_cream: :shaved_ice: :birthday: :cake: :cookie: :chocolate_bar: :candy: :lollipop: :honey_pot: :apple: :green_apple: :tangerine: :lemon: :cherries: :grapes: :watermelon: :strawberry: :peach: :melon: :banana: :pear: :pineapple: :sweet_potato: :eggplant: :tomato: :corn: Places

:house: :house_with_garden: :school: :office: :post_office: :hospital: :bank: :convenience_store: :love_hotel: :hotel: :wedding: :church: :department_store: :european_post_office: :city_sunrise: :city_sunset: :japanese_castle: :european_castle: :tent: :factory: :tokyo_tower: :japan: :mount_fuji: :sunrise_over_mountains: :sunrise: :stars: :statue_of_liberty: :bridge_at_night: :carousel_horse: :rainbow: :ferris_wheel: :fountain: :roller_coaster: :ship: :speedboat: :boat: :sailboat: :rowboat: :anchor: :rocket: :airplane: :helicopter: :steam_locomotive: :tram: :mountain_railway: :bike: :aerial_tramway: :suspension_railway: :mountain_cableway: :tractor: :blue_car: :oncoming_automobile: :car: :red_car: :taxi: :oncoming_taxi: :articulated_lorry: :bus: :oncoming_bus: :rotating_light: :police_car: :oncoming_police_car: :fire_engine: :ambulance: :minibus: :truck: :train: :station: :train2: :bullettrain_front: :bullettrain_side: :light_rail: :monorail: :railway_car: :trolleybus: :ticket: :fuelpump: :vertical_traffic_light: :traffic_light: :warning: :construction: :beginner: :atm: :slot_machine: :busstop: :barber: :hotsprings: :checkered_flag: :crossed_flags: :izakaya_lantern: :moyai: :circus_tent: :performing_arts: :round_pushpin: :triangular_flag_on_post: :jp: :kr: :cn: :us: :fr: :es: :it: :ru: :gb: :uk: :de: Symbols

:one: :two: :three: :four: :five: :six: :seven: :eight: :nine: :keycap_ten: :1234: :zero: :hash: :symbols: :arrow_backward: :arrow_down: :arrow_forward: :arrow_left: :capital_abcd: :abcd: :abc: :arrow_lower_left: :arrow_lower_right: :arrow_right: :arrow_up: :arrow_upper_left: :arrow_upper_right: :arrow_double_down: :arrow_double_up: :arrow_down_small: :arrow_heading_down: :arrow_heading_up: :leftwards_arrow_with_hook: :arrow_right_hook: :left_right_arrow: :arrow_up_down: :arrow_up_small: :arrows_clockwise: :arrows_counterclockwise: :rewind: :fast_forward: :information_source: :ok: :twisted_rightwards_arrows: :repeat: :repeat_one: :new: :top: :up: :cool: :free: :ng: :cinema: :koko: :signal_strength: :u5272: :u5408: :u55b6: :u6307: :u6708: :u6709: :u6e80: :u7121: :u7533: :u7a7a: :u7981: :sa: :restroom: :mens: :womens: :baby_symbol: :no_smoking: :parking: :wheelchair: :metro: :baggage_claim: :accept: :wc: :potable_water: :put_litter_in_its_place: :secret: :congratulations: :m: :passport_control: :left_luggage: :customs: :ideograph_advantage: :cl: :sos: :id: :no_entry_sign: :underage: :no_mobile_phones: :do_not_litter: :non-potable_water: :no_bicycles: :no_pedestrians: :children_crossing: :no_entry: :eight_spoked_asterisk: :sparkle: :eight_pointed_black_star: :heart_decoration: :vs: :vibration_mode: :mobile_phone_off: :chart: :currency_exchange: :aries: :taurus: :gemini: :cancer: :leo: :virgo: :libra: :scorpius: :sagittarius: :capricorn: :aquarius: :pisces: :ophiuchus: :six_pointed_star: :negative_squared_cross_mark: :a: :b: :ab: :o2: :diamond_shape_with_a_dot_inside: :recycle: :end: :back: :on: :soon: :clock1: :clock130: :clock10: :clock1030: :clock11: :clock1130: :clock12: :clock1230: :clock2: :clock230: :clock3: :clock330: :clock4: :clock430: :clock5: :clock530: :clock6: :clock630: :clock7: :clock730: :clock8: :clock830: :clock9: :clock930: :heavy_dollar_sign: :copyright: :registered: :tm: :x: :heavy_exclamation_mark: :bangbang: :interrobang: :o: :heavy_multiplication_x: :heavy_plus_sign: :heavy_minus_sign: :heavy_division_sign: :white_flower: :100: :heavy_check_mark: :ballot_box_with_check: :radio_button: :link: :curly_loop: :wavy_dash: :part_alternation_mark: :trident: :black_small_square: :white_small_square: :black_medium_small_square: :white_medium_small_square: :black_medium_square: :white_medium_square: :black_large_square: :white_large_square: :white_check_mark: :black_square_button: :white_square_button: :black_circle: :white_circle: :red_circle: :large_blue_circle: :large_blue_diamond: :large_orange_diamond: :small_blue_diamond: :small_orange_diamond: :small_red_triangle: :small_red_triangle_down: :shipit:)
';
    
       
        
    //public $integration_results = array();

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client(); 
//        $this->client = new GuzzleHttp\Client([
//            'base_url' => 'http://localhost',
//            'defaults' => ['exceptions' => false]
//        ]);
        $this->json_schema = '{
          "start":"2014-03-04T22:31:35+00:00",
          "end":"2014-03-04T22:31:35+00:00",
          "incident_id": "1231231123",
          "content":"Short description about alert ended",
          "alert_policy_name":"Default application alert policy",
          "account_name":"Account name",
          "severity":"Critical",
          "message":"Message about alert",  
          "long_description":"Long description about alert ended",
          "url":"http://custompath",
          "application_name":"Custom",
          "username": "josegarcia"
        }';

        $this->emoji_array = explode(' ', $this->emoji_list);

        $this->emoji_packs = array_chunk($this->emoji_array, 5);

        

    }




    
    //public function deleteTests()
    public function deleteTests()
    {
        $config = array(
            'username' => '',
            'password' => '',
            'dbname' => 'timelinedb',
            //'cn' 	   => sprintf('mongodb://%s:%d/%s', $hosts, $port,$database),
            'connection_string'=> sprintf('mongodb://%s:%d/','127.0.0.1','27017'),
            'integrationsColl' => 'INTERNAL_integrations'
        );
        
        $store = new DB($config);
        //($store);
        var_dump($store);
        $collections = $store->db->getCollectionNames();        
        foreach ($collections as $coll) 
        {
            if (strpos( $coll, $this->test_spacename) === 0)
            {                
                //$store->db->$coll->drop();
                $delete_url = $this->api_url . $this->integration_path . 'remove';
                var_dump($delete_url);                
                $post_body = array(
                    'integration_name' => $coll
                );

                var_dump($post_body);
                $response = $this->client->post($delete_url, ['json' => $post_body]);                
                $this->assertEquals(200, $response->getStatusCode());
                echo "$coll  deleted!\n";
            }
        }
    }
    
        
    
    
    
    public function test_integrations()
    //public function integrations()
    {
        
        for ($x=0; $x<$this->number_tests; $x++)
        {
            $unique_id = uniqid();
            $integration_name = $this->test_spacename . $unique_id;                        
            $url = $this->api_url . $this->integration_path . 'add';

            var_dump($url);

            $username = 'phpunit_user_' . $unique_id;
            $post_body = array(
                'integration_name' => $integration_name,
                'created_by'=> $username, 
                'type' => 'custom'
            );

            echo (json_encode($post_body, true));     	    

            $response = $this->client->post($url, [
                'json' => $post_body
            ]);

            var_dump(json_decode($response->getBody(), true));

            $this->assertEquals(200, $response->getStatusCode());

            $body = $response->getBody();

            $body_data = json_decode($body, true);

            //($body_data);
            
            $apiKey = $body_data['apiKey'];

            sleep(10);
            
            $this->generate_docs($apiKey, $username, $unique_id, $integration_name);
            
            //$this->searches($integration_name);
            
            $this->all_unique_searchs = array();
                        
            
        }  
            
    }
    
    public function generate_docs($apiKey, $username, $unique_id, $integration_name, $custom_url='')
    {  
        $webhook_ulr = $this->external_url . "/webhooks/$integration_name/$apiKey";
        
        var_dump($webhook_ulr);
                        
        for ($x=0; $x<$this->number_docs; $x++ )
        {
            $search_data = array();
            
            $search_data['common_id'] = $unique_id;
            
            $unique_search_string = $unique_id . "NTEST_$x";
            
            $search_data['search_string'] = $unique_search_string;
            
            $str_test = "CUSTOMTESTING: $unique_id NTEST_$x";

            $modified_schema = str_replace('Short description about alert ended', $str_test, $this->json_schema );

            $modified_schema = str_replace('1231231123', "{$unique_id}{$x}", $modified_schema );

            //$rstate = $this->get_random_state();

            //$modified_schema = str_replace('OPEN', $rstate, $modified_schema );

            $random_day = rand(1, 30) + $x;

            $random_hours = rand(1, 24) + $x;

            $random_seconds = rand(1, 60) + $x;
            
            //$new_date = date(DATE_ISO8601, strtotime("-$random_time day -$random_time hours -$random_time seconds"));

            $custom_hour = $x + 2;

            $new_date = date(DATE_ISO8601, strtotime("+$custom_hour hours"));

            var_dump('NEW DATE');
            var_dump($new_date);
                                    
            $search_data['date'] = $new_date;
            
            $modified_schema = str_replace("2014-03-04T22:31:35+00:00", $new_date, $modified_schema);

            // if (array_key_exists($x, $this->emoji_packs))
            // {
            //     $emojis = implode($this->emoji_packs[$x]);                
            // }
            // else
            // {
            //     $emojis = '';
            // }

            $emojis = implode($this->emoji_packs[array_rand($this->emoji_packs)]);


            //$modified_schema = str_replace("Message about alert", "$emojis  DELETED state requested by Pavel Arlovich &lt;pavelarlovich@cloud.upwork.com&gt;. <http://stash.odesk.com/projects/US/repos/clotho/commits/8fa0398|view commit> Making change to *bold* text and _italic_ text and ~tilded~  and this is for some `single backticks` inline code and this: ```triple backticks``` to create a block preformatted  " . $unique_id, $modified_schema);
            
            $this->all_unique_searchs[] = $search_data;
            
            $testing_array = json_decode($modified_schema, true);           
            
	       
	
            if ($custom_url)
            {
                $incident = $testing_array['messages'][0]['data']['incident'];
        
                $data_needed = array(
                    "username"=>  $this->get_next_user(),
                    "event" => "Making change to *bold* text and _italic_ text and ~tilded~  and some > to indent a single line and this to indent >>> multiple line\n like this\n and this \n and this is for some `single backticks` inline code and this: ```triple backticks``` to create a block preformatted " . $unique_id,
                    "start_date" => $new_date
                );

                $testing_array = array_merge($incident, $data_needed);
                //$testing_array = $this->$custom_function($testing_array, $unique_id, $new_date);
                                
            }
            
            var_dump($webhook_ulr);
            echo(json_encode($testing_array));

            $response_webhook = $this->client->post($webhook_ulr, [
                    'json' => $testing_array
                ]);
            
            $hook_body = $response_webhook->getBody();
            
            var_dump('HOOKBODY:');
            echo $hook_body;

            $hook_response_data = json_decode($hook_body, true);

            $this->assertEquals(200, $response_webhook->getStatusCode());

	        var_dump('STATUS', $response_webhook->getStatusCode());

            if ($this->pause_documents)
            {
                sleep($this->pause_documents);  
            }

            

            //var_dump($hook_response_data);
                        
        }
                
    }
    
    public function searches($integration_name)
    {
        
        $counter = 1;
            
        foreach ($this->all_unique_searchs as $search_data)
        {            
            
            $search_api_url = $this->api_url . "/webhooks/integrations/$integration_name/search";
            
            var_dump($search_api_url);
            
            $valid_search_post = array(
                "search" => $search_data['common_id'],
                "type" => "pagerduty",
                "start_date" => $search_data['date'],
                "end_date" => date(DATE_ISO8601)
            );
            
            var_dump(json_encode($valid_search_post));
            
            $response_search = $this->client->post($search_api_url, [
                'json' => $valid_search_post
            ]);
            
            $body_search = json_decode($response_search->getBody(), true);
            
            //var_dump($body_search);
            
            $search_results = count($body_search);
            
            var_dump($search_results);
            
            
            $calculated_result = (2 * $counter);
            
            var_dump('CALCULATED: '. $calculated_result  . " Real:  $search_results");
            
            $counter++;
                                    
            //$this->assertEquals($calculated_result, $search_results);
            
            //$this->assertNotEmpty($body_search);

        }
        
        
                
        
            
    }
        
    public function common_events()                                                        
    //public function test_common_events()    
    {
	 /* this is necessary to create the common integration
         //  POST http://localhost/api/integration/add
         * {
                "integration_name": "common_integration",
                "created_by": "GOD",
                "type": "custom"
           }
         * 
         * result:
            {
                "integration_name": "common_integration",
                "user": "GOD",
                "apiKey": "S07795bVYm4rT058kft50Rhq822a8BF9446zAV2103814dcsfg",
                "createdOn": "2015-10-02T00:22:26+0200",
                "type": "custom",
                "_id": "560db2227bcfb6e4238b4573"
            }

         * 
         * Common integrations must have 
         */
        
        $integration_name = 'common_integration';
        
        //$apiKey = 'S07795bVYm4rT058kft50Rhq822a8BF9446zAV2103814dcsfg';
	    $apiKey = 'e5CIK9Xgx9n9PnH3G192ueR426oxf31u7f48L0xaG16iSCt9W4';
        
        $webhook_ulr =  "$this->api_url/webhooks/$integration_name/$apiKey";
        
        $unique_id = uniqid();
        
        $this->generate_docs($apiKey, 'GOD', $unique_id , $integration_name, $webhook_ulr);
        
        $this->searches($integration_name);
        
        
        
        
    }

        /*
        $this->assertArrayHasKey('bookId', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('author', $data);
        $this->assertEquals(42, $data['price']);
         * 
         */    
    
    
    function get_next_user()
    {
        $current_user = array_shift($this->all_users);
        $this->all_users[] = $current_user;
        return $current_user;
    }


    function get_random_state()
    {
        $rand_key = array_rand($this->type_entries);
        $rand_value = $this->type_entries[$rand_key];
        return ($rand_value);
    }
    
    
    
}

























//$m = new Mongo();
//
//$db = $m->learningmongo;
//
//$people = $db->people;
//
//$people->insert(array('name'=> 'Pepe', 'trabajo' => 'recogedor de cartones'));
//
//$cursor = $people->find();
//
//
//if ($cursor->count() > 0)
//{
//    foreach ($cursor as $doc)
//    {
//        echo $doc['name'];
//    }
//}
