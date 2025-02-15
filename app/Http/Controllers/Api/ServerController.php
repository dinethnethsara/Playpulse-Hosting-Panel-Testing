<?php

namespace PlayPulse\Http\Controllers\Api;

use PlayPulse\Http\Controllers\Controller;
use PlayPulse\Services\ServerManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerController extends Controller
{
	protected $serverManager;

	public function __construct(ServerManager $serverManager)
	{
		$this->serverManager = $serverManager;
	}

	public function create(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:50',
			'game' => 'required|string',
			'cpu' => 'required|string',
			'memory' => 'required|string',
			'disk' => 'required|string'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		try {
			$serverId = $this->serverManager->createServer($request->all());
			return response()->json([
				'success' => true,
				'server_id' => $serverId,
				'message' => 'Server created successfully'
			], 201);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Server creation failed',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function status($serverId)
	{
		$status = $this->serverManager->getServerStatus($serverId);
		if ($status === 'not_found') {
			return response()->json(['error' => 'Server not found'], 404);
		}
		
		$monitoring = $this->serverManager->monitorServer($serverId);
		return response()->json([
			'status' => $status,
			'monitoring' => $monitoring
		]);
	}

	public function updateResources(Request $request, $serverId)
	{
		$validator = Validator::make($request->all(), [
			'cpu' => 'string',
			'memory' => 'string',
			'disk' => 'string'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		$updated = $this->serverManager->updateServerResources($serverId, $request->all());
		if (!$updated) {
			return response()->json(['error' => 'Server not found'], 404);
		}

		return response()->json(['message' => 'Resources updated successfully']);
	}

	public function listGames()
	{
		return response()->json([
			'games' => $this->serverManager->getAvailableGames()
		]);
	}
}