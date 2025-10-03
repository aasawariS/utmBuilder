<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\utmLink;
use Illuminate\Support\Facades\Validator;

class UtmController extends Controller
{
    protected function buildUtmQuery(array $params): string
    {
        $map = [
            'author' => 'utm_source',
            'resource_type' => 'utm_medium',
            'campaign' => 'utm_campaign',
            'slug' => 'utm_content',
            'title' => 'utm_term'
        ];

        $parts = [];
        foreach ($map as $field => $utmKey) {
            $value = trim($params[$field] ?? '');
            if ($value !== '') {
                $value = preg_replace('/\s+/', '-', $value);
                $value = rawurlencode($value);
                $parts[] = "{$utmKey}={$value}";
            }
        }

        return implode('&', $parts);
    }

    protected function appendUtmToUrl(string $url, string $query): string
    {
        if (empty($query)) {
            return $url;
        }
        $sep = (parse_url($url, PHP_URL_QUERY) ? '&' : '?');
        return $url . $sep . $query;
    }

    public function showForm()
    {
        return view('utm.form');
    }

    public function generateSingle(Request $request)
    {
        $data = $request->only(['url','author','title','slug','resource_type','campaign']);
        $validator = Validator::make($data, [
            'url' => 'required|url|max:2048',
            'author' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'resource_type' => 'nullable|string|max:255',
            'campaign' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query = $this->buildUtmQuery($data);
        $utmUrl = $this->appendUtmToUrl($data['url'], $query);

        utmLink::create([
            'author' => $data['author'] ?? null,
            'title' => $data['title'] ?? null,
            'slug' => $data['slug'] ?? null,
            'resource_type' => $data['resource_type'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'original_url' => $data['url'],
            'utm_url' => $utmUrl,
            'created_by_ip' => $request->ip(),
        ]);

        return redirect()->route('utm.form')->with('success_single', $utmUrl);
    }

    public function generateFromParagraph(Request $request)
    {
        $data = $request->only(['paragraph','author','title','slug','resource_type','campaign']);
        $validator = Validator::make($data, [
            'paragraph' => 'required|string',
            'author' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'resource_type' => 'nullable|string|max:255',
            'campaign' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $paragraph = $data['paragraph'];
        $regex = '/\b((?:https?:\/\/)[^\s<>\'""]+)/i';
        $matches = [];
        preg_match_all($regex, $paragraph, $matches);
        $urls = $matches[1] ?? [];

        $replacements = [];
        foreach ($urls as $url) {
            $query = $this->buildUtmQuery($data);
            $utmUrl = $this->appendUtmToUrl($url, $query);

            utmLink::create([
                'author' => $data['author'] ?? null,
                'title' => $data['title'] ?? null,
                'slug' => $data['slug'] ?? null,
                'resource_type' => $data['resource_type'] ?? null,
                'campaign' => $data['campaign'] ?? null,
                'original_url' => $url,
                'utm_url' => $utmUrl,
                'context_text' => substr($paragraph, 0, 2000),
                'created_by_ip' => $request->ip(),
            ]);

            $replacements[$url] = $utmUrl;
        }

        $modified = strtr($paragraph, $replacements);
        return redirect()->route('utm.form')->with('success_paragraph', $modified);
    }

    public function preview(Request $request)
    {
        $params = $request->only(['author','title','slug','resource_type','campaign','url']);
        $query = $this->buildUtmQuery($params);
        $utmUrl = $this->appendUtmToUrl($params['url'] ?? '', $query);
        return response()->json(['utm' => $utmUrl]);
    }
}
