<template>
	<div class="bg-gray-50 dark:bg-gray-900">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
			<!-- Server Status Card -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-semibold text-gray-700 dark:text-white">Server Status</h3>
					<div :class="{'bg-green-500': server.isOnline, 'bg-red-500': !server.isOnline}"
							 class="w-3 h-3 rounded-full"></div>
				</div>
				<div class="mt-4">
					<p class="text-sm text-gray-600 dark:text-gray-300">{{ server.name }}</p>
					<p class="text-xl font-bold text-gray-800 dark:text-white">{{ server.status }}</p>
				</div>
			</div>

			<!-- Resource Usage Cards -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-white">CPU Usage</h3>
				<div class="mt-4">
					<div class="relative pt-1">
						<div class="flex mb-2 items-center justify-between">
							<div>
								<span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
									{{ server.cpu_usage }}%
								</span>
							</div>
						</div>
						<div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
							<div :style="{ width: server.cpu_usage + '%' }"
									 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500">
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Quick Actions -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-white">Quick Actions</h3>
				<div class="mt-4 grid grid-cols-2 gap-2">
					<button @click="startServer" 
									class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
						Start
					</button>
					<button @click="stopServer" 
									class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
						Stop
					</button>
					<button @click="restartServer" 
									class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded">
						Restart
					</button>
					<button @click="openConsole" 
									class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
						Console
					</button>
				</div>
			</div>

			<!-- Player Count -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-white">Players Online</h3>
				<div class="mt-4">
					<p class="text-3xl font-bold text-gray-800 dark:text-white">
						{{ server.player_count }}/{{ server.max_players }}
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-300">Active Players</p>
				</div>
			</div>
		</div>

		<!-- Performance Graphs -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-white">Resource Usage History</h3>
				<canvas ref="resourceChart"></canvas>
			</div>
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-white">Network Traffic</h3>
				<canvas ref="networkChart"></canvas>
			</div>
		</div>
	</div>
</template>

<script>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

export default {
	name: 'ServerDashboard',
	props: {
		server: {
			type: Object,
			required: true,
			default: () => ({
				isOnline: false,
				name: '',
				status: 'Offline',
				cpu_usage: 0,
				player_count: 0,
				max_players: 0
			})
		}
	},
	setup(props) {
		const resourceChart = ref(null)
		const networkChart = ref(null)

		const initCharts = () => {
			// Resource Usage Chart
			const resourceCtx = resourceChart.value.getContext('2d')
			new Chart(resourceCtx, {
				type: 'line',
				data: {
					labels: ['1m', '2m', '3m', '4m', '5m'],
					datasets: [{
						label: 'CPU Usage',
						data: [30, 45, 25, 60, 35],
						borderColor: 'rgb(59, 130, 246)',
						tension: 0.4
					}]
				},
				options: {
					responsive: true,
					plugins: {
						legend: {
							position: 'top',
						}
					}
				}
			})

			// Network Traffic Chart
			const networkCtx = networkChart.value.getContext('2d')
			new Chart(networkCtx, {
				type: 'line',
				data: {
					labels: ['1m', '2m', '3m', '4m', '5m'],
					datasets: [{
						label: 'Network In',
						data: [100, 200, 150, 300, 250],
						borderColor: 'rgb(34, 197, 94)',
						tension: 0.4
					}, {
						label: 'Network Out',
						data: [50, 100, 75, 150, 125],
						borderColor: 'rgb(239, 68, 68)',
						tension: 0.4
					}]
				},
				options: {
					responsive: true,
					plugins: {
						legend: {
							position: 'top',
						}
					}
				}
			})
		}

		const startServer = async () => {
			try {
				await fetch(`/api/servers/${props.server.id}/start`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				})
			} catch (error) {
				console.error('Failed to start server:', error)
			}
		}

		const stopServer = async () => {
			try {
				await fetch(`/api/servers/${props.server.id}/stop`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				})
			} catch (error) {
				console.error('Failed to stop server:', error)
			}
		}

		const restartServer = async () => {
			try {
				await fetch(`/api/servers/${props.server.id}/restart`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				})
			} catch (error) {
				console.error('Failed to restart server:', error)
			}
		}

		const openConsole = () => {
			window.open(`/servers/${props.server.id}/console`, '_blank')
		}

		onMounted(() => {
			initCharts()
		})

		return {
			resourceChart,
			networkChart,
			startServer,
			stopServer,
			restartServer,
			openConsole
		}
	}
}
</script>