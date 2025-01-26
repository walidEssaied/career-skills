import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    UserIcon,
    ChatBubbleLeftIcon,
    UserPlusIcon,
    CheckCircleIcon,
} from '@heroicons/react/24/outline';

interface Connection {
    id: number;
    name: string;
    role: string;
    avatar: string;
    status: 'connected' | 'pending' | 'none';
}

interface Message {
    id: number;
    sender_id: number;
    receiver_id: number;
    content: string;
    created_at: string;
}

const NetworkingDashboard: React.FC = () => {
    const [connections, setConnections] = useState<Connection[]>([]);
    const [messages, setMessages] = useState<Message[]>([]);
    const [selectedUser, setSelectedUser] = useState<Connection | null>(null);
    const [messageInput, setMessageInput] = useState('');

    const handleSendMessage = async () => {
        if (!messageInput.trim() || !selectedUser) return;

        try {
            // TODO: Implement send message functionality
            setMessageInput('');
        } catch (error) {
            console.error('Failed to send message:', error);
        }
    };

    const handleConnect = async (userId: number) => {
        try {
            // TODO: Implement connect functionality
        } catch (error) {
            console.error('Failed to connect:', error);
        }
    };

    return (
        <div className="container mx-auto px-4 py-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Connections List */}
                <div className="col-span-1 bg-white rounded-lg shadow overflow-hidden">
                    <div className="p-4 border-b border-gray-200">
                        <h2 className="text-lg font-semibold text-gray-900">Connections</h2>
                    </div>
                    <div className="divide-y divide-gray-200">
                        {connections.map((connection) => (
                            <div
                                key={connection.id}
                                className={`p-4 hover:bg-gray-50 cursor-pointer ${
                                    selectedUser?.id === connection.id ? 'bg-gray-50' : ''
                                }`}
                                onClick={() => setSelectedUser(connection)}
                            >
                                <div className="flex items-center space-x-3">
                                    <img
                                        src={connection.avatar}
                                        alt={connection.name}
                                        className="h-10 w-10 rounded-full"
                                    />
                                    <div className="flex-1">
                                        <p className="text-sm font-medium text-gray-900">
                                            {connection.name}
                                        </p>
                                        <p className="text-sm text-gray-500">{connection.role}</p>
                                    </div>
                                    {connection.status === 'pending' && (
                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Chat Area */}
                <div className="col-span-2 bg-white rounded-lg shadow overflow-hidden">
                    {selectedUser ? (
                        <>
                            <div className="p-4 border-b border-gray-200">
                                <div className="flex items-center space-x-3">
                                    <img
                                        src={selectedUser.avatar}
                                        alt={selectedUser.name}
                                        className="h-10 w-10 rounded-full"
                                    />
                                    <div>
                                        <h2 className="text-lg font-semibold text-gray-900">
                                            {selectedUser.name}
                                        </h2>
                                        <p className="text-sm text-gray-500">{selectedUser.role}</p>
                                    </div>
                                </div>
                            </div>
                            <div className="flex-1 p-4 space-y-4 h-[400px] overflow-y-auto">
                                {messages.map((message) => (
                                    <div
                                        key={message.id}
                                        className={`flex ${
                                            message.sender_id === selectedUser.id
                                                ? 'justify-start'
                                                : 'justify-end'
                                        }`}
                                    >
                                        <div
                                            className={`rounded-lg px-4 py-2 max-w-xs ${
                                                message.sender_id === selectedUser.id
                                                    ? 'bg-gray-100'
                                                    : 'bg-blue-500 text-white'
                                            }`}
                                        >
                                            <p className="text-sm">{message.content}</p>
                                            <p className="text-xs mt-1 opacity-70">
                                                {new Date(message.created_at).toLocaleTimeString()}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            <div className="p-4 border-t border-gray-200">
                                <div className="flex space-x-3">
                                    <input
                                        type="text"
                                        value={messageInput}
                                        onChange={(e) => setMessageInput(e.target.value)}
                                        placeholder="Type a message..."
                                        className="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <button
                                        onClick={handleSendMessage}
                                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Send
                                    </button>
                                </div>
                            </div>
                        </>
                    ) : (
                        <div className="flex items-center justify-center h-[500px] text-gray-500">
                            Select a connection to start chatting
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default NetworkingDashboard;
