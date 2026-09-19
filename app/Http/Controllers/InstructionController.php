<?php

namespace App\Http\Controllers;

use App\Models\Content\Instruction;
use Inertia\Inertia;

class InstructionController extends Controller
{
    public function index()
    {
        $instructions = Instruction::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($i) => [
                'slug' => $i->slug,
                'title' => $i->title,
            ]);

        return Inertia::render('Instructions/Index', ['instructions' => $instructions]);
    }

    public function show(string $slug)
    {
        $instruction = Instruction::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('Instructions/Show', [
            'instruction' => [
                'title' => $instruction->title,
                'content' => $instruction->content,
                'video_url' => $instruction->video_url,
            ],
        ]);
    }
}
