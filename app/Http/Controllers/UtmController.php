<?php

namespace App\Http\Controllers;

use App\Models\UtmLink;
use Illuminate\Http\Request;

class UtmController extends Controller
{
    private function buildUtm(array $data): string {
        $map = [
            'author'        => 'utm_source',
            'resource_type' => 'utm_medium',
            'campaign'      => 'utm_campaign',
            'slug'          => 'utm_content',
            'title'         => 'utm_term'
        ];
        $parts = [];
        foreach ($map as $field => $utmKey) {
            if (!empty($data[$field])) {
                $parts[] = $utmKey . '=' . rawurlencode(str_replace(' ','-',$data[$field]));
            }
        }
        return implode('&',$parts);
    }

    private function append(string $url,string $query): string {
        if (!$query) return $url;
        $sep = (parse_url($url, PHP_URL_QUERY) ? '&' : '?');
        return $url.$sep.$query;
    }

    // show UI
    public function showForm() {
        return view('utm.form');
    }

    // single URL mode
    public function generateSingle(Request $req) {
        $data = $req->only(['url','author','title','slug','resource_type','campaign']);
        $utmUrl = $this->append($data['url'],$this->buildUtm($data));

        UtmLink::create([
            'author'=>$data['author'],'title'=>$data['title'],'slug'=>$data['slug'],
            'resource_type'=>$data['resource_type'],'campaign'=>$data['campaign'],
            'original_url'=>$data['url'],'utm_url'=>$utmUrl
        ]);

        return back()->with('utm_single',$utmUrl);
    }

    // paragraph mode
    public function generateParagraph(Request $req): \Illuminate\Http\RedirectResponse
    {
        $data = $req->only(['paragraph','author','title','slug','resource_type','campaign']);
        $paragraph = $data['paragraph'];

        preg_match_all('/\bhttps?:\/\/[^\s<>\'"]+/i',$paragraph,$matches);
        $urls = $matches[0] ?? [];

        foreach($urls as $url){
            $utmUrl = $this->append($url,$this->buildUtm($data));
            $paragraph = str_replace($url,$utmUrl,$paragraph);

            UtmLink::create([
                'author'=>$data['author'],'title'=>$data['title'],'slug'=>$data['slug'],
                'resource_type'=>$data['resource_type'],'campaign'=>$data['campaign'],
                'original_url'=>$url,'utm_url'=>$utmUrl,'context_text'=>substr($data['paragraph'],0,2000)
            ]);
        }
        return back()->with('utm_paragraph',$paragraph);
    }
}

