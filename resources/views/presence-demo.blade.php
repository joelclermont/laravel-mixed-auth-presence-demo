<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mixed Auth Presence Channel Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .user-list {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            min-height: 300px;
        }
        .user-item {
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-item.authenticated {
            background-color: #e0f2fe;
            border: 1px solid #0284c7;
        }
        .user-item.guest {
            background-color: #f3f4f6;
            border: 1px solid #9ca3af;
        }
        .user-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
        }
        .badge-authenticated {
            background-color: #0284c7;
            color: white;
        }
        .badge-guest {
            background-color: #6b7280;
            color: white;
        }
        .current-user {
            font-weight: bold;
        }
        .notification {
            padding: 0.5rem 1rem;
            margin: 0.5rem 0;
            border-radius: 4px;
            animation: slideIn 0.3s ease-out;
        }
        .notification.join {
            background-color: #d1fae5;
            border: 1px solid #10b981;
        }
        .notification.leave {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
        }
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div x-data="presenceChannel()" style="max-width: 800px; margin: 2rem auto; padding: 1rem;">
        <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 1rem;">Mixed Auth Presence Channel Demo</h1>

        <div style="background-color: #f9fafb; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <p><strong>You are:</strong>
                <span>{{ $currentUser['name'] }}</span>
                <span class="user-badge badge-{{ $currentUser['type'] }}">{{ ucfirst($currentUser['type']) }}</span>
            </p>
            @if($currentUser['type'] === 'guest')
                <p style="margin-top: 0.5rem; color: #6b7280;">
                    <a href="/login" style="color: #0284c7; text-decoration: underline;">Login</a> to appear as an authenticated user
                </p>
            @else
                <form method="POST" action="/logout" style="margin-top: 0.5rem;">
                    @csrf
                    <button type="submit" style="background-color: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; border: none; cursor: pointer;">
                        Logout
                    </button>
                </form>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">
                    Active Users (<span x-text="users.length"></span>)
                </h2>
                <div class="user-list">
                    <template x-for="user in users" :key="user.id">
                        <div :class="'user-item ' + user.type">
                            <span x-text="user.name + (user.id === '{{ $currentUser['id'] }}' ? ' (You)' : '')" :class="user.id === '{{ $currentUser['id'] }}' ? 'current-user' : ''"></span>
                            <span :class="'user-badge badge-' + user.type" x-text="user.type === 'authenticated' ? 'User' : 'Guest'"></span>
                        </div>
                    </template>
                    <div x-show="users.length === 0" style="color: #9ca3af; text-align: center; margin-top: 2rem;">
                        Connecting to channel...
                    </div>
                </div>
            </div>

            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Activity Log</h2>
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 1rem; min-height: 300px; max-height: 300px; overflow-y: auto;">
                    <template x-for="(notification, index) in notifications" :key="index">
                        <div :class="'notification ' + notification.type">
                            <span x-text="notification.message"></span>
                            <span style="color: #6b7280; font-size: 0.875rem; margin-left: 0.5rem;" x-text="notification.time"></span>
                        </div>
                    </template>
                    <div x-show="notifications.length === 0" style="color: #9ca3af; text-align: center; margin-top: 2rem;">
                        Waiting for activity...
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px;">
            <h3 style="font-weight: bold; margin-bottom: 0.5rem;">How to test:</h3>
            <ol style="list-style: decimal; margin-left: 1.5rem;">
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
