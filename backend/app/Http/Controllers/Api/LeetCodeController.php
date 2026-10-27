<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeetCodeService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class LeetCodeController extends Controller
{
    public function skillStats(
        string $username,
        LeetCodeService $leetCodeService
    ) {
        try {
            $stats = $leetCodeService->getSkillStats($username);

            return response()->json($stats);
        } catch (RequestException $exception) {
            $status = $exception->response->status();

            if ($status === 429) {
                return response()->json([
                    'message' => 'LeetCode API rate limit reached. Try again later.',
                ], 503);
            }

            return response()->json([
                'message' => 'Unable to retrieve LeetCode skill statistics.',
            ], 502);
        } catch (ConnectionException $exception) {
            return response()->json([
                'message' => 'LeetCode API is temporarily unavailable.',
            ], 503);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }






public function submissions(
    string $username,
    LeetCodeService $leetCodeService
) {
    try {
        $data = $leetCodeService->getSubmissions($username);

        return response()->json($data);
    } catch (\Illuminate\Http\Client\RequestException $exception) {
        if ($exception->response->status() === 429) {
            return response()->json([
                'message' => 'LeetCode API rate limit reached. Try again later.',
            ], 503);
        }

        return response()->json([
            'message' => 'Unable to retrieve LeetCode submissions.',
        ], 502);
    } catch (\Illuminate\Http\Client\ConnectionException $exception) {
        return response()->json([
            'message' => 'LeetCode API is temporarily unavailable.',
        ], 503);
    } catch (\Throwable $exception) {
        report($exception);

        return response()->json([
            'message' => 'An unexpected error occurred.',
        ], 500);
    }
}

}
