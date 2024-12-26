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
    public function videoCompress(Request $request): JsonResponse
    {      
        $request->validate([
            'File' => 'required|file|mimes:mp4,mov,avi|max:20480', 
        ]);
        if (!$request->hasFile('File')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('File');
        $fileName = time() . '.' . $file->extension();
        $upload_path = '/Users/zbg/my-laravel-app/storage/video_compression/input';
        $file->move($upload_path, $fileName);
        $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.mp4';

        $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(500);
        FFMpeg::fromDisk('video_compression_input')
            ->open( $uploadedFileName)
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 720));
            })
            ->export()
            ->toDisk('video_compression_output')
            ->inFormat($lowBitrateFormat)
            ->save(pathinfo($uploadedFileName, PATHINFO_FILENAME) . '.mp4');

        return response()->json([
            'message' => 'Video processed and saved successfully.',
        ]);


    }


    public function videoHLS(Request $request): JsonResponse
    {

        $request->validate([
            'File' => 'required|file|mimes:mp4|max:20480', 
            ]);
        if (!$request->hasFile('File')) {
            return response()->json(['error' => 'No file uploaded'], 400);
            }

        $file = $request->file('File');
        $fileName = time() . '.' . $file->extension();
        $upload_path = '/Users/zbg/my-laravel-app/storage/video_HLS/input';
        $file->move($upload_path, $fileName);
        $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.mp4';

        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::fromDisk('video_hls_input')
            ->open($uploadedFileName)
            ->exportForHLS()
            ->setSegmentLength(10)
            ->setKeyFrameInterval(48)
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->toDisk('video_hls_output')
            ->save(pathinfo($uploadedFileName, PATHINFO_FILENAME) . '.m3u8');

        return response()->json([
            'message' => 'Video processed and saved successfully.',
        ]);

    }


    public function videoHLS2(Request $request): JsonResponse
    {

        $request->validate([
            'File' => 'required|file|mimes:mp4|max:20480', 
            ]);
        if (!$request->hasFile('File')) {
            return response()->json(['error' => 'No file uploaded'], 400);
            }

        $file = $request->file('File');
        $fileName = time() . '.' . $file->extension();
        $upload_path = '/Users/zbg/my-laravel-app/storage/video_HLS_2/input';
        $file->move($upload_path, $fileName);
        $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.mp4';

        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);
        $superBitrate = (new X264)->setKiloBitrate(1500);

        FFMpeg::fromDisk('video_hls2_input')
            ->open($uploadedFileName)
            ->exportForHLS()
            ->addFormat($lowBitrate, function ($media) {
                $media->addFilter('scale=640:480');
            })
            ->addFormat($midBitrate, function ($media) {
                $media->scale(960, 720);
            })
            ->addFormat($highBitrate, function ($media) {
                $media->addFilter(function ($filters, $in, $out) {
                    $filters->custom($in, 'scale=1920:1200', $out);
                });
            })
            ->addFormat($superBitrate, function ($media) {
                $media->addLegacyFilter(function ($filters) {
                    $filters->resize(new \FFMpeg\Coordinate\Dimension(2560, 1920));
                });
            })
            ->toDisk('video_hls2_output')
            ->save(pathinfo($uploadedFileName, PATHINFO_FILENAME) . '.m3u8');

        return response()->json([
            'message' => 'Video processed and saved successfully.',
        ]);

    }


    public function videoHLS_demo(Request $request): JsonResponse
    {

        $upload_path = '/Users/zbg/my-laravel-app/storage/video_HLS/input';
        $file = $request->file('File');
        $fileName = time() . '.' . $file->extension();
        $file->move($upload_path, $fileName);
        $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.mp4';

        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::fromDisk('video_hls_input')
            ->open($uploadedFileName)
            ->exportForHLS()
            ->setSegmentLength(10)
            ->setKeyFrameInterval(48)
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->toDisk('video_hls2_output')
            ->save(pathinfo($uploadedFileName, PATHINFO_FILENAME) . '.m3u8');

        return response()->json([
            'message' => 'Video processed and saved successfully.',

        ]);

    }




}
