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
        FFMpeg::fromDisk('video_compression_input')
            ->open('small.mp4')
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 720));
            })
            ->export()
            ->toDisk('video_compression_output')
            ->inFormat($lowBitrateFormat)
            ->save('small_out_n.mp4');  

        return response()->json([
            'message' => 'Video processed and saved successfully.',
            //'path' => asset('storage/' . $docPath),
        ]);

        
    }

    public function videoHLS(Request $request): JsonResponse
    {
        $file = $request->file('File');
        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::fromDisk('video_hls_input')
            ->open('small.mp4')
            ->exportForHLS()
            ->setSegmentLength(10) 
            ->setKeyFrameInterval(48) 
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->toDisk('video_hls_output')
            ->save('small_out.m3u8');

        return response()->json([
            'message' => 'Video processed and saved successfully.',
            //'path' => asset('storage/' . $docPath),
        ]);
    
    }

    public function videoHlsEncryption(Request $request): JsonResponse
    {
        $file = $request->file('File');


    }




}
