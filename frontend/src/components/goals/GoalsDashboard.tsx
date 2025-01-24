import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import { fetchGoals, createGoal, updateGoal, deleteGoal } from '../../store/slices/goalsSlice';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js';
import { Doughnut } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement);

interface NewGoalFormData {
    title: string;
    description: string;
    target_date: string;
    career_path_id: string;
    priority: string;
}

const GoalsDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: goals, loading, error } = useSelector((state: RootState) => state.goals);
    const [showNewGoalForm, setShowNewGoalForm] = useState(false);
    const [newGoalData, setNewGoalData] = useState<NewGoalFormData>({
        title: '',
        description: '',
        target_date: '',
        career_path_id: '',
        priority: 'medium',
    });

    useEffect(() => {
        dispatch(fetchGoals());
    }, [dispatch]);

    const handleCreateGoal = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            await dispatch(createGoal(newGoalData)).unwrap();
            setShowNewGoalForm(false);
            setNewGoalData({
                title: '',
                description: '',
                target_date: '',
                career_path_id: '',
                priority: 'medium',
            });
        } catch (error) {
            // Error handling is done in the goals slice
        }
    };

    const getGoalStatusData = () => {
        const statusCounts = goals.reduce((acc: { [key: string]: number }, goal) => {
            acc[goal.status] = (acc[goal.status] || 0) + 1;
            return acc;
        }, {});

        return {
            labels: ['Not Started', 'In Progress', 'Completed', 'On Hold'],
            datasets: [{
                data: [
                    statusCounts['not_started'] || 0,
                    statusCounts['in_progress'] || 0,
                    statusCounts['completed'] || 0,
                    statusCounts['on_hold'] || 0,
                ],
                backgroundColor: [
                    '#bae6fd', // Light blue
                    '#7dd3fc', // Blue
                    '#38bdf8', // Medium blue
                    '#0ea5e9', // Dark blue
                ],
            }],
        };
    };

    if (loading) {
        return <div className="flex justify-center items-center h-64">Loading...</div>;
    }

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div className="md:flex md:items-center md:justify-between">
                <div className="flex-1 min-w-0">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Career Goals
                    </h2>
                </div>
                <div className="mt-4 flex md:mt-0 md:ml-4">
                    <button
                        type="button"
                        onClick={() => setShowNewGoalForm(true)}
                        className="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Add New Goal
                    </button>
                </div>
            </div>

            <div className="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
                {/* Goals Overview Chart */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Goals Overview</h3>
                    <div className="h-64">
                        <Doughnut data={getGoalStatusData()} />
                    </div>
                </div>

                {/* New Goal Form */}
                {showNewGoalForm && (
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium text-gray-900 mb-4">Create New Goal</h3>
                        <form onSubmit={handleCreateGoal} className="space-y-4">
                            <div>
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    value={newGoalData.title}
                                    onChange={(e) => setNewGoalData({ ...newGoalData, title: e.target.value })}
                                    className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                />
                            </div>
                            <div>
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    value={newGoalData.description}
                                    onChange={(e) => setNewGoalData({ ...newGoalData, description: e.target.value })}
                                    rows={3}
                                    className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                />
                            </div>
                            <div>
                                <label htmlFor="target_date" className="block text-sm font-medium text-gray-700">
                                    Target Date
                                </label>
                                <input
                                    type="date"
                                    id="target_date"
                                    value={newGoalData.target_date}
                                    onChange={(e) => setNewGoalData({ ...newGoalData, target_date: e.target.value })}
                                    className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                />
                            </div>
                            <div>
                                <label htmlFor="priority" className="block text-sm font-medium text-gray-700">
                                    Priority
                                </label>
                                <select
                                    id="priority"
                                    value={newGoalData.priority}
                                    onChange={(e) => setNewGoalData({ ...newGoalData, priority: e.target.value })}
                                    className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div className="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onClick={() => setShowNewGoalForm(false)}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Create Goal
                                </button>
                            </div>
                        </form>
                    </div>
                )}
            </div>

            {/* Goals List */}
            <div className="mt-8">
                <div className="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul className="divide-y divide-gray-200">
                        {goals.map((goal) => (
                            <li key={goal.id}>
                                <div className="px-4 py-4 sm:px-6">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center">
                                            <div className="flex-shrink-0">
                                                <div
                                                    className={`h-8 w-8 rounded-full flex items-center justify-center ${
                                                        goal.status === 'completed'
                                                            ? 'bg-green-100 text-green-800'
                                                            : goal.status === 'in_progress'
                                                            ? 'bg-blue-100 text-blue-800'
                                                            : 'bg-gray-100 text-gray-800'
                                                    }`}
                                                >
                                                    {goal.status === 'completed' ? '✓' : goal.progress + '%'}
                                                </div>
                                            </div>
                                            <div className="ml-4">
                                                <h4 className="text-lg font-medium text-gray-900">{goal.title}</h4>
                                                <p className="mt-1 text-sm text-gray-500">{goal.description}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    goal.priority === 'high'
                                                        ? 'bg-red-100 text-red-800'
                                                        : goal.priority === 'medium'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-green-100 text-green-800'
                                                }`}
                                            >
                                                {goal.priority}
                                            </span>
                                            <button
                                                onClick={() => dispatch(deleteGoal(goal.id))}
                                                className="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div className="mt-2">
                                        <div className="relative pt-1">
                                            <div className="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                                <div
                                                    style={{ width: `${goal.progress}%` }}
                                                    className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary-500"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </div>
    );
};

export default GoalsDashboard;
