<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects','media')) {
                $table->json('media')->nullable()->after('body');
            }
        });

        // Backfill si anciennes colonnes présentes
        $hasGallery = Schema::hasColumn('projects','gallery');
        $hasVideo   = Schema::hasColumn('projects','video_urls');

        if ($hasGallery || $hasVideo) {
            $cols = ['id'];
            if ($hasGallery) $cols[] = 'gallery';
            if ($hasVideo)   $cols[] = 'video_urls';

            DB::table('projects')
                ->select($cols)
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($hasGallery, $hasVideo) {
                    foreach ($rows as $row) {
                        $media = [];

                        if ($hasGallery && !empty($row->gallery)) {
                            foreach (json_decode($row->gallery, true) ?? [] as $p) {
                                $media[] = ['type'=>'image','path'=>$p];
                            }
                        }
                        if ($hasVideo && !empty($row->video_urls)) {
                            foreach (json_decode($row->video_urls, true) ?? [] as $u) {
                                if ($u) $media[] = ['type'=>'video','path'=>$u];
                            }
                        }

                        if ($media) {
                            DB::table('projects')->where('id', $row->id)->update(['media'=>json_encode($media)]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects','media')) {
                $table->dropColumn('media');
            }
        });
    }
};
