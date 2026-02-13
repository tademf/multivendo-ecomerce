<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NewsController extends Controller
{
    /**
     * Display a listing of the news (Public)
     */
    public function index()
    {
        $news = News::active()
                    ->latest('published_at')
                    ->paginate(12);

        return Inertia::render('NewsPage', [
            'news' => $news->through(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'excerpt' => $item->excerpt,
                    'image' => $item->image_url,
                    'published_at' => $item->formatted_published_date,
                    'is_active' => $item->isActive(),
                    'created_at' => $item->created_at->format('F j, Y')
                ];
            }),
            'filters' => request()->all(['search']),
        ]);
    }

    /**
     * Display the specified news (Public)
     */
    public function show(News $news)
    {
        if (!$news->isActive()) {
            return redirect()->route('news.index')
                ->with('error', 'News article not found or expired.');
        }

        return Inertia::render('NewsDetailPage', [
            'news' => [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'image' => $news->image_url,
                'published_at' => $news->formatted_published_date,
                'created_at' => $news->created_at->format('F j, Y')
            ],
            'related_news' => News::active()
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'excerpt' => $item->excerpt(100),
                        'image' => $item->image_url,
                        'published_at' => $item->formatted_published_date
                    ];
                })
        ]);
    }

    /**
     * Show create form (Admin only)
     */
    public function create()
    {
        return Inertia::render('Admin/News/Create');
    }

    /**
     * Store a newly created news (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'expired_at' => 'nullable|date|after:published_at',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public');
            $data['image'] = $path;
        }

        News::create($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show edit form (Admin only)
     */
    public function edit(News $news)
    {
        return Inertia::render('Admin/News/Edit', [
            'news' => $news
        ]);
    }

    /**
     * Update the specified news (Admin only)
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'expired_at' => 'nullable|date|after:published_at',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            
            $path = $request->file('image')->store('news', 'public');
            $data['image'] = $path;
        }

        $news->update($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news (Admin only)
     */
    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    /**
     * Admin index (Admin only)
     */
    public function adminIndex()
    {
        $news = News::latest()
                    ->paginate(15);

        return Inertia::render('Admin/News/Index', [
            'news' => $news
        ]);
    }
}