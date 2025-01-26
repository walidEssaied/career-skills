import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    Goal,
    fetchGoals,
    createGoal,
    updateGoal,
    deleteGoal,
    updateGoalProgress,
} from '../../store/slices/goalsSlice';
import GoalStatistics from './GoalStatistics';

const GoalsDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: goals, loading } = useSelector((state: RootState) => state.goals);
    
    const [newGoal, setNewGoal] = useState<Partial<Goal>>({
        title: '',
        description: '',
        status: 'not_started',
        priority: 'medium',
        progress: 0,
        target_date: '',
        notes: '',
    });

    useEffect(() => {
        dispatch(fetchGoals());
    }, [dispatch]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            await dispatch(createGoal(newGoal)).unwrap();
            setNewGoal({
                title: '',
                description: '',
                status: 'not_started',
                priority: 'medium',
                progress: 0,
                target_date: '',
                notes: '',
            });
        } catch (error) {
            console.error('Failed to create goal:', error);
        }
    };

    const handleProgressUpdate = async (id: number, progress: number) => {
        try {
            await dispatch(updateGoalProgress({ id, progress })).unwrap();
        } catch (error) {
            console.error('Failed to update progress:', error);
        }
    };

    const handleStatusUpdate = async (id: number, status: Goal['status']) => {
        try {
            await dispatch(updateGoal({ id, data: { status } })).unwrap();
        } catch (error) {
            console.error('Failed to update status:', error);
        }
    };

    const handleDelete = async (id: number) => {
        if (window.confirm('Are you sure you want to delete this goal?')) {
            try {
                await dispatch(deleteGoal(id)).unwrap();
            } catch (error) {
                console.error('Failed to delete goal:', error);
            }
        }
    };

    return (
        <div className="container mx-auto px-4 py-8">
            <div className="mb-8">
                <GoalStatistics />
            </div>

            {/* Add New Goal Form */}
            <div className="bg-white rounded-lg shadow p-6 mb-8">
                <h2 className="text-xl font-semibold mb-4 text-black">Add New Goal</h2>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Title</label>
                        <input
                            type="text"
                            value={newGoal.title}
                            onChange={(e) => setNewGoal({ ...newGoal, title: e.target.value })}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            value={newGoal.description}
                            onChange={(e) => setNewGoal({ ...newGoal, description: e.target.value })}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            rows={3}
                            required
                        />
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Status</label>
                            <select
                                value={newGoal.status}
                                onChange={(e) => setNewGoal({ ...newGoal, status: e.target.value as Goal['status'] })}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Priority</label>
                            <select
                                value={newGoal.priority}
                                onChange={(e) => setNewGoal({ ...newGoal, priority: e.target.value as Goal['priority'] })}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Target Date</label>
                            <input
                                type="date"
                                value={newGoal.target_date}
                                onChange={(e) => setNewGoal({ ...newGoal, target_date: e.target.value })}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            value={newGoal.notes ?? "lkqsjdlqksjdlqskdjqsmldkj"}
                            onChange={(e) => setNewGoal({ ...newGoal, notes: e.target.value })}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            rows={2}
                        />
                    </div>
                    <div className="flex justify-end">
                        <button
                            type="submit"
                            className="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Add Goal
                        </button>
                    </div>
                </form>
            </div>

            {/* Goals List */}
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="px-6 py-4 border-b">
                    <h2 className="text-xl font-semibold text-black">Your Goals</h2>
                </div>
                <div className="divide-y divide-gray-200">
                    {goals.map((goal) => (
                        <div key={goal.id} className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-lg font-medium text-black">{goal.title}</h3>
                                <div className="flex items-center space-x-4">
                                    <select
                                        value={goal.status}
                                        onChange={(e) => handleStatusUpdate(goal.id, e.target.value as Goal['status'])}
                                        className="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="not_started">Not Started</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="on_hold">On Hold</option>
                                    </select>
                                    <button
                                        onClick={() => handleDelete(goal.id)}
                                        className="text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <p className="text-gray-600 mb-4">{goal.description}</p>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-sm">
                                    <span className='text-black font-semibold'>Progress</span>
                                    <span>{goal.progress}%</span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={goal.progress}
                                    onChange={(e) => handleProgressUpdate(goal.id, parseInt(e.target.value))}
                                    className="w-full"
                                />
                            </div>
                            <div className="mt-4 flex items-center text-sm text-gray-500 space-x-4">
                                <span>Priority: {goal.priority}</span>
                                <span>Target: {new Date(goal.target_date).toLocaleDateString()}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default GoalsDashboard;
