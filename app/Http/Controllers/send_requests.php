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
        $img = $body->findOne('.post-toggle > div:nth-child(1)')->find('img');
        $image_link=[];
        if($img != null){
            foreach($img as $key=>$value){
                array_push($image_link,$value->getAttribute('src'));
            }
        }
        $name = explode(" ", delete_space($body->findOne('#topic > div:nth-child(1) > div:nth-child(1) > a:nth-child(2)')->text()));
        $array_question += ['data_question' => ['person' => [
            'name' => $name[0],
            'level' => $name[1],
            'role' => $name[2],
            'register'=>$name[3].$name[4],
            'post'=>$name[5].$name[6].$name[7]
        ],
        'image_link'=>$image_link,
        'question_topic' => $question_topic, 'question' => $question]];
        $image_link_comment=[];
        $solutions = $body->findOne('#posts')->find('article');
        foreach($solutions as $key=>$val){
            $image_link_comment=[];
            $img = $val->find('img');
            if($img != null){
                foreach($img as $key=>$value){
                    if($value->getAttribute('src')!="https://s1.ninifile.com/statics/default/blank-loading.png?width=80&amp;height=85&amp;crop&amp;bgcolor=white"){
                        array_push($image_link_comment,$value->getAttribute('src'));
                    }
                }
            }
            $name_commented = explode(" ", delete_space($val->findOne("div:nth-child(1) > div:nth-child(1)")->text()));
            array_push($array_question,['name' => $name_commented[0],'comment'=>$val->findOne('.post-message')->text(),'image_link'=>$image_link_comment]);
        }

        return response()->json($array_question,200);
    }
}

