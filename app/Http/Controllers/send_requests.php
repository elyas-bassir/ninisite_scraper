<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http as http;
use \HTMLDomParser\DomFactory as find_element;

use function Laravel\Prompts\text;
use function PHPSTORM_META\type;

class send_requests extends Controller
{
    public static $url = "https://www.ninisite.com/discussion/topic/";
    public function get_comments($id)
    {
        function delete_space($text)
        {
            if (strpos($text, "  ") > 0) {
                $text = str_replace("  ", " ", $text);
            }
            if (strpos($text, "  ") > 0) {
                return delete_space($text);
                exit();
            }
            return $text;
        }
        $array_question = [];
        $response = http::get(self::$url . $id);
        $body = find_element::load($response->body());
        $question_topic = $body->findOne(".topic-title")->text();
        $question = $body->findOne('.post-toggle > div:nth-child(1)')->text();
        $name = explode(" ", delete_space($body->findOne('#topic > div:nth-child(1) > div:nth-child(1) > a:nth-child(2)')->text()));
        $array_question += ['data_question' => ['person' => [
            'name' => $name[0],
            'level' => $name[1],
            'role' => $name[2],
            'register'=>$name[3].$name[4],
            'post'=>$name[5].$name[6].$name[7]
        ], 'question_topic' => $question_topic, 'question' => $question]];

        $solutions = $body->findOne('#posts')->find('article');
        foreach($solutions as $key=>$val){
            $name_commented = explode(" ", delete_space($val->findOne("div:nth-child(1) > div:nth-child(1)")->text()));
            array_push($array_question,['name' => $name_commented[0],'comment'=>$val->findOne('.post-message')->text()]);
        }

        return response()->json($array_question,200);
    }
}

