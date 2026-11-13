<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Naturally Ventilated Polyhouse (NVPH)',
            'Fan & Pad Polyhouse',
            'Nethouse',
            'Polynet House',
            'Wire Rope Nethouse',
            'Glasshouse',
            'Outdoor Hydroponics System',
            'Cocopeat Based System',
            'Soil-Based Growing System',
            'Indoor Vertical Farming',
        ];

        $categoryModels = [];
        foreach ($categories as $catName) {
            $categoryModels[$catName] = ProjectCategory::firstOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'name'        => $catName,
                    'description' => "Commercial {$catName} structure solutions.",
                    'status'      => true,
                ]
            );
        }

        $defaultCat = $categoryModels['Naturally Ventilated Polyhouse (NVPH)'] ?? null;

        Project::updateOrCreate(
            ['slug' => 'sharma-floriculture-farm'],
            [
                'title'               => 'Sharma Floriculture Farm',
                'subtitle'            => 'Protected Farming',
                'excerpt'             => 'A 4,000 sq. m. gerbera polyhouse built near Indore — from structure and fertigation to year-round export-grade harvests.',
                'content'             => '<p class="fm-text">A 4,000 sq. m. naturally ventilated polyhouse for commercial gerbera cultivation near Indore.</p><p class="fm-text">Scope included GI structure, 200-micron UV polythene, 40-mesh insect net, drip-fertigation with venturi, raised beds and trellising.</p><p class="fm-text">The farm now harvests export-grade gerbera stems year-round, supplying Indore and Bhopal flower markets.</p>',
                'client_name'         => 'Sharma Floriculture Farm',
                'location'            => 'Indore, Madhya Pradesh',
                'crop'                => 'Gerbera',
                'project_category_id' => $defaultCat ? $defaultCat->id : null,
                'project_type'        => $defaultCat ? $defaultCat->name : 'Naturally Ventilated Polyhouse (NVPH)',
                'area'                => '4,000 sq. m.',
                'project_status'      => 'Completed',
                'completed_at'        => '2024-03-15',
                'featured_image'      => 'https://fai.codespine.in/images/proj-gerbera.jpg',
                'banner_image'        => 'https://fai.codespine.in/images/banner3.jpeg',
                'gallery_images'      => [
                    'https://fai.codespine.in/images/proj-gerbera.jpg',
                    'https://fai.codespine.in/images/proj-gerbera-2.jpg',
                    'https://fai.codespine.in/images/fip-structures.jpg',
                    'https://fai.codespine.in/images/fip-irrigation-fertigation.jpg',
                    'https://fai.codespine.in/images/pp-nvph.jpg',
                    'https://fai.codespine.in/images/project-overview-farm.jpg'
                ],
                'contact_phone'       => '+91 7879183931',
                'contact_whatsapp'    => '917879183931',
                'meta_title'          => 'Sharma Floriculture Farm - Project Details | Future Agri Infra',
                'meta_description'    => 'A 4,000 sq. m. gerbera polyhouse built near Indore — from structure and fertigation to year-round export-grade harvests.',
                'meta_keywords'       => 'floriculture, gerbera polyhouse, protected farming indore, turnkey polyhouse',
                'is_featured'         => true,
                'status'              => true,
                'sort_order'          => 1,
            ]
        );
    }
}
