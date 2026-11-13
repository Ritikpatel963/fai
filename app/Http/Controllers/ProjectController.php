<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display the specified project case study.
     */
    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return view('projects.show', compact('project'));
    }
}

