<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        @if ($url['lastmod'])
        <lastmod>{{ $url['lastmod'] }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
