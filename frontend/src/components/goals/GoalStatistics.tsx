import React, { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import { fetchGoalStatistics } from '../../store/slices/goalsSlice';

const GoalStatistics: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { statistics, loading } = useSelector((state: RootState) => state.goals);

    useEffect(() => {
        dispatch(fetchGoalStatistics());
    }, [dispatch]);

    if (loading || !statistics) {
        return (
            <div className="animate-pulse">
                <div className="h-32 bg-gray-200 rounded"></div>
            </div>
        );
    }

    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h2 className="text-xl font-semibold mb-4 text-black">Goal Statistics</h2>
            
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div className="bg-blue-50 p-4 rounded-lg">
                    <p className="text-sm text-blue-600">Total Goals</p>
                    <p className="text-2xl font-bold text-blue-700">{statistics.total_goals}</p>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                    <p className="text-sm text-green-600">Completed</p>
                    <p className="text-2xl font-bold text-green-700">{statistics.completed_goals}</p>
                </div>
                <div className="bg-yellow-50 p-4 rounded-lg">
                    <p className="text-sm text-yellow-600">In Progress</p>
                    <p className="text-2xl font-bold text-yellow-700">{statistics.in_progress_goals}</p>
                </div>
                <div className="bg-purple-50 p-4 rounded-lg">
                    <p className="text-sm text-purple-600">Completion Rate</p>
                    <p className="text-2xl font-bold text-purple-700">
                        {statistics.completion_rate}%
                    </p>
                </div>
            </div>
        </div>
    );
};

export default GoalStatistics;
