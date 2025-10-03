@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">UTM Link Generator</h1>

    @if(session('success_single'))
        <div class="mb-4 p-3 bg-green-100 border rounded">
            <strong>Generated UTM:</strong>
            <div class="break-words mt-2">{{ session('success_single') }}</div>
        </div>
    @endif

    @if(session('success_paragraph'))
        <div class="mb-4 p-3 bg-green-100 border rounded">
            <strong>Modified Paragraph (UTMs replaced):</strong>
            <div class="mt-2 whitespace-pre-wrap">{{ session('success_paragraph') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border rounded">
            <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-6">
        <div class="col-span-1">
            <form method="POST" action="{{ route('utm.single') }}" id="single-form">
                @csrf
                <h2 class="font-semibold mb-2">Single URL</h2>

                <label class="block">Original URL
                    <input type="url" name="url" id="single-url" value="{{ old('url') }}" class="w-full p-2 border rounded" placeholder="https://example.com/page">
                </label>

                <label class="block mt-3">Author (utm_source)
                    <input type="text" name="author" value="{{ old('author') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Title
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Slug (utm_content)
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Resource Type (utm_medium)
                    <input type="text" name="resource_type" value="{{ old('resource_type') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Campaign (utm_campaign)
                    <input type="text" name="campaign" value="{{ old('campaign') }}" class="w-full p-2 border rounded">
                </label>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Generate UTM</button>
                    <button type="button" id="preview-single" class="px-3 py-2 rounded border">Preview</button>
                </div>

                <div id="preview-output" class="mt-3 text-sm text-gray-700"></div>
            </form>
        </div>

        <div class="col-span-1">
            <form method="POST" action="{{ route('utm.paragraph') }}" id="paragraph-form">
                @csrf
                <h2 class="font-semibold mb-2">Paragraph (auto-detect links)</h2>

                <label class="block">Paragraph (paste long text)
                    <textarea name="paragraph" id="paragraph-input" rows="12" class="w-full p-2 border rounded">{{ old('paragraph') }}</textarea>
                </label>

                <label class="block mt-3">Author (utm_source)
                    <input type="text" name="author" id="p-author" value="{{ old('author') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Slug (utm_content)
                    <input type="text" name="slug" id="p-slug" value="{{ old('slug') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Resource Type (utm_medium)
                    <input type="text" name="resource_type" id="p-resource" value="{{ old('resource_type') }}" class="w-full p-2 border rounded">
                </label>

                <label class="block mt-3">Campaign (utm_campaign)
                    <input type="text" name="campaign" id="p-campaign" value="{{ old('campaign') }}" class="w-full p-2 border rounded">
                </label>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Replace Links & Save</button>
                    <button type="button" id="detect-links" class="px-3 py-2 rounded border">Detect Links</button>
                </div>

                <div id="detected-links" class="mt-3 text-sm text-gray-700"></div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('preview-single').addEventListener('click', function(){
    const form = document.getElementById('single-form');
    const data = new FormData(form);
    fetch("{{ route('utm.preview') }}", {
        method: "POST",
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        body: data
    }).then(r => r.json()).then(json => {
        document.getElementById('preview-output').textContent = json.utm || 'No preview';
    });
});

document.getElementById('detect-links').addEventListener('click', function(){
    const text = document.getElementById('paragraph-input').value || '';
    const regex = /\b((?:https?:\/\/)[^\s<>'"]+)/ig;
    let matches = [...text.matchAll(regex)].map(m => m[1]);
    const container = document.getElementById('detected-links');
    if(matches.length === 0) {
        container.textContent = 'No links found.';
        return;
    }
    container.innerHTML = '<strong>Detected links:</strong><ul>' + matches.map(u => `<li><a href="${u}" target="_blank">${u}</a></li>`).join('') + '</ul>';
});
</script>
@endsection
