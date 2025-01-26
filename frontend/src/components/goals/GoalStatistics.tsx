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
            <h2 className="text-xl font-semibold mb-4">Goal Statistics</h2>
            
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div className="bg-blue-50 p-4 rounded-lg">
                    <p className="text-sm text-blue-600">Total Goals</p>
                    <p className="text-2xl font-bold text-blue-700">{statistics.total_goals}</p>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                    <p className="text-sm text-green-600">Completed</p>
                    <p className="text-2xl font-bold text-green-700">{statistics.completed_goals}</p>
                </div>
                <div className="bg-purple-50 p-4 rounded-lg">
                    <p className="text-sm text-purple-600">Average Progress</p>
                    <p className="text-2xl font-bold text-purple-700">
                        {Math.round(statistics.average_progress)}%
                    </p>
                </div>
            </div>

            <div className="space-y-4">
                <h3 className="text-lg font-medium">Status Breakdown</h3>
                {statistics.by_status.map((stat) => (
                    <div key={stat.status} className="flex items-center">
                        <div className="flex-1">
                            <div className="flex justify-between mb-1">
                                <span className="text-sm font-medium text-gray-700">
                                    {stat.status.replace('_', ' ').toUpperCase()}
                                </span>
                                <span className="text-sm font-medium text-gray-700">
                                    {stat.count}
                                </span>
                            </div>
                            <div className="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    className="bg-blue-600 h-2 rounded-full"
                                    style={{
                                        width: `${(stat.count / statistics.total_goals) * 100}%`
                                    }}
                                ></div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default GoalStatistics;
