<?php

namespace App\Http\Controllers;

use FFMpeg;
use Illuminate\Http\Request;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use FFMpeg\Filters\Video\VideoFilters;

class VideoController extends Controller
{
    public function videoCompress(Request $request):JsonResponse
    {

        $request->validate([
            'File' => 'required|file|mimes:mp4,mov,avi|max:20480', // Example validation rules
        ]);
 
        
    
        // Check if the file exists
        if (!$request->hasFile('File')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('File');
        $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(500);
        FFMpeg::fromDisk('public')
            ->open('small.mp4')
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 720));
            })
            ->export()
            ->toDisk('public')
            ->inFormat($lowBitrateFormat)

            //->inFormat(new \FFMpeg\Format\Video\X264)
            ->save('small_out.mp4');

        return response()->json([
            'message' => 'Video processed and saved successfully.',
            //'path' => asset('storage/' . $docPath),
        ]);

        
    }
}
