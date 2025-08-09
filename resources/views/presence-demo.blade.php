<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mixed Auth Presence Channel Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div x-data="presenceChannel()" class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Mixed Auth Presence Channel Demo</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <p class="flex items-center gap-2">
                <span class="font-semibold">You are:</span>
                <span>{{ $user->name }}</span>
                @if($user->getAttribute('is_guest'))
                    <span class="inline-block px-2 py-1 text-xs font-bold text-white bg-gray-600 rounded">Guest</span>
                @else
                    <span class="inline-block px-2 py-1 text-xs font-bold text-white bg-blue-600 rounded">User</span>
                @endif
            </p>
            @if($user->getAttribute('is_guest'))
                <p class="mt-2 text-gray-600">
                    <a href="/login" class="text-blue-600 hover:underline">Login</a> to appear as an authenticated user
                </p>
            @else
                <form method="POST" action="/logout" class="mt-2">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                        Logout
                    </button>
                </form>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-bold mb-3">
                    Active Users (<span x-text="users.length"></span>)
                </h2>
                <div class="bg-white rounded-lg shadow p-4 min-h-[300px]">
                    <template x-for="user in users" :key="user.id">
                        <div class="flex items-center justify-between p-3 mb-2 rounded-lg transition"
                             :class="user.type === 'authenticated' ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200'">
                            <span x-text="user.name + (user.id == {{ $user->id }} ? ' (You)' : '')"
                                  :class="user.id == {{ $user->id }} ? 'font-bold' : ''"></span>
                            <span class="px-2 py-1 text-xs font-bold rounded"
                                  :class="user.type === 'authenticated' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white'"
                                  x-text="user.type === 'authenticated' ? 'User' : 'Guest'"></span>
                        </div>
                    </template>
                    <div x-show="users.length === 0" class="text-gray-400 text-center mt-8">
                        Connecting to channel...
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-3">Activity Log</h2>
                <div class="bg-white rounded-lg shadow p-4 min-h-[300px] max-h-[300px] overflow-y-auto">
                    <template x-for="(notification, index) in notifications" :key="index">
                        <div class="p-3 mb-2 rounded-lg"
                             :class="notification.type === 'join' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                            <span x-text="notification.message"></span>
                            <span class="text-gray-500 text-sm ml-2" x-text="notification.time"></span>
                        </div>
                    </template>
                    <div x-show="notifications.length === 0" class="text-gray-400 text-center mt-8">
                        Waiting for activity...
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 p-6 bg-amber-50 border border-amber-200 rounded-lg">
            <h3 class="font-bold mb-3">How to test:</h3>
            <ol class="list-decimal list-inside space-y-1 text-gray-700">
                <li>Open this page in multiple browser tabs/windows or incognito mode</li>
                <li>Watch as users join and leave the presence channel</li>
                <li>Try logging in/out to see how user types change</li>
                <li>Notice how both guests and authenticated users appear together</li>
            </ol>
        </div>
    </div>

    <script>
        function presenceChannel() {
            return {
                users: [],
                notifications: [],
                channel: null,

                init() {
                    this.connectToChannel();
                },

                connectToChannel() {
                    this.channel = window.Echo.join('mixed-auth-presence-demo')
                        .here((users) => {
                            if (!Array.isArray(users)) {
                                users = users ? [users] : [];
                            }
                            this.users = users;
                            this.addNotification(`Connected to channel (${users.length} user${users.length !== 1 ? 's' : ''} online)`, 'join');
                        })
                        .joining((user) => {
                            if (!this.users.find(u => u.id === user.id)) {
                                this.users.push(user);
                                this.addNotification(`${user.name} joined`, 'join');
                            }
                        })
                        .leaving((user) => {
                            this.users = this.users.filter(u => u.id !== user.id);
                            this.addNotification(`${user.name} left`, 'leave');
                        })
                        .error((error) => {
                            console.error('Channel error:', error);
                            this.addNotification('Connection error', 'leave');
                        });
                },

                addNotification(message, type) {
                    const time = new Date().toLocaleTimeString();
                    this.notifications.unshift({ message, type, time });
                    if (this.notifications.length > 10) {
                        this.notifications.pop();
                    }
                }
            }
        }
    </script>
</body>
</html>