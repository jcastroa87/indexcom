@extends('layouts.app')

@section('title', 'API Documentation - IndexCom')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">API Documentation</h1>

            <div class="card">
                <div class="card-body api-documentation">
                    <div id="markdown-content" class="api-docs-content">
                        {{ $apiDocContent }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const content = document.getElementById('markdown-content').textContent;
        document.getElementById('markdown-content').innerHTML = marked.parse(content);

        // Add syntax highlighting for code blocks
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<style>
    .api-documentation {
        font-size: 0.95rem;
    }

    .api-docs-content h1 {
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
    }

    .api-docs-content h2 {
        font-size: 1.8rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eaecef;
    }

    .api-docs-content h3 {
        font-size: 1.4rem;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .api-docs-content pre {
        background-color: #f6f8fa;
        border-radius: 6px;
        padding: 16px;
        overflow: auto;
    }

    .api-docs-content code {
        background-color: rgba(27, 31, 35, 0.05);
        border-radius: 3px;
        padding: 0.2em 0.4em;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
    }

    .api-docs-content pre code {
        background-color: transparent;
        padding: 0;
    }

    .api-docs-content blockquote {
        padding: 0 1em;
        color: #6a737d;
        border-left: 0.25em solid #dfe2e5;
        margin: 0 0 16px 0;
    }

    .api-docs-content table {
        display: block;
        width: 100%;
        overflow: auto;
        margin-top: 0;
        margin-bottom: 16px;
        border-spacing: 0;
        border-collapse: collapse;
    }

    .api-docs-content table th,
    .api-docs-content table td {
        padding: 6px 13px;
        border: 1px solid #dfe2e5;
    }

    .api-docs-content table tr {
        background-color: #fff;
        border-top: 1px solid #c6cbd1;
    }

    .api-docs-content table tr:nth-child(2n) {
        background-color: #f6f8fa;
    }
</style>
@endpush
