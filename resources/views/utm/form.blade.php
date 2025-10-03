@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-3xl bg-white shadow-md border border-gray-200 rounded-2xl p-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">UTM Generator</h1>

            {{-- Nav Tabs with border --}}
            <div class="flex mb-6 border-b border-gray-300">
                <button id="tab-single"
                        class="flex-1 py-2 text-center font-semibold border-b-2 border-blue-600 text-blue-600"
                        onclick="showTab('single')">
                    Single URL
                </button>
                <button id="tab-paragraph"
                        class="flex-1 py-2 text-center font-semibold text-gray-500 hover:text-blue-600 border-b-2 border-transparent"
                        onclick="showTab('paragraph')">
                    Paragraph
                </button>
            </div>

            {{-- Single URL Form --}}
            <form method="POST" action="{{ route('utm.single') }}" id="form-single" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600">Base URL</label>
                    <input type="url" name="url" placeholder="https://example.com"
                           class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                </div>

                <fieldset class="border border-gray-200 rounded-lg p-4">
                    <legend class="text-sm font-medium text-gray-600 px-2">UTM Parameters</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block text-sm text-gray-600">Author (utm_source)</label>
                            <input type="text" name="author" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Resource Type (utm_medium)</label>
                            <input type="text" name="resource_type" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Campaign (utm_campaign)</label>
                            <input type="text" name="campaign" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Title Slug (utm_content)</label>
                            <input type="text" name="slug" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm text-gray-600">Keywords (utm_term, optional)</label>
                        <input type="text" name="title" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </fieldset>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold">
                    Generate UTM Link
                </button>
            </form>

            {{-- Generated link (single mode) --}}
            @if(session('utm_single'))
                <div class="mt-6 p-4 bg-gray-50 border rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Generated Link</label>
                    <div class="flex items-center">
                        <input id="generatedSingle" type="text" readonly
                               value="{{ session('utm_single') }}"
                               class="flex-1 px-3 py-2 border rounded-l-lg bg-white">
                        <button onclick="copyToClipboard('generatedSingle')"
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                            Copy
                        </button>
                    </div>
                </div>
            @endif

            {{-- Paragraph Form --}}
            <form method="POST" action="{{ route('utm.paragraph') }}" id="form-paragraph" class="space-y-4 hidden mt-8">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600">Paragraph</label>
                    <textarea name="paragraph" rows="5" placeholder="Paste text with links..."
                              class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300"></textarea>
                </div>

                <fieldset class="border border-gray-200 rounded-lg p-4">
                    <legend class="text-sm font-medium text-gray-600 px-2">UTM Parameters</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block text-sm text-gray-600">Author (utm_source)</label>
                            <input type="text" name="author" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Resource Type (utm_medium)</label>
                            <input type="text" name="resource_type" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Campaign (utm_campaign)</label>
                            <input type="text" name="campaign" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Title Slug (utm_content)</label>
                            <input type="text" name="slug" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </fieldset>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold">
                    Replace Links with UTMs
                </button>
            </form>

            {{-- Generated paragraph --}}
            @if(session('utm_paragraph'))
                <div class="mt-6 p-4 bg-gray-50 border rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Modified Paragraph</label>
                    <textarea id="generatedParagraph" readonly rows="5"
                              class="w-full px-3 py-2 border rounded-lg bg-white">{{ session('utm_paragraph') }}</textarea>
                    <button onclick="copyToClipboard('generatedParagraph')"
                            class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold">
                        Copy Paragraph
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showTab(tab) {
            // Reset
            document.getElementById('tab-single').className =
                "flex-1 py-2 text-center font-semibold text-gray-500 hover:text-blue-600 border-b-2 border-transparent";
            document.getElementById('tab-paragraph').className =
                "flex-1 py-2 text-center font-semibold text-gray-500 hover:text-blue-600 border-b-2 border-transparent";

            document.getElementById('form-single').classList.add('hidden');
            document.getElementById('form-paragraph').classList.add('hidden');

            // Activate
            if(tab==='single'){
                document.getElementById('form-single').classList.remove('hidden');
                document.getElementById('tab-single').className =
                    "flex-1 py-2 text-center font-semibold border-b-2 border-blue-600 text-blue-600";
            } else {
                document.getElementById('form-paragraph').classList.remove('hidden');
                document.getElementById('tab-paragraph').className =
                    "flex-1 py-2 text-center font-semibold border-b-2 border-blue-600 text-blue-600";
            }
        }

        function copyToClipboard(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
        }
    </script>
@endsection
