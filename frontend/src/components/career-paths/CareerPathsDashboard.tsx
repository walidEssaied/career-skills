import { AcademicCapIcon, BriefcaseIcon, ChartBarIcon } from '@heroicons/react/24/outline';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    CareerPath,
    fetchCareerPaths,
    fetchUserCareerPaths,
    joinCareerPath,
    leaveCareerPath,
} from '../../store/slices/careerPathsSlice';
import { analyzeSkillGaps } from '../../store/slices/mlSlice';

const CareerPathsDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: careerPaths, userPaths, loading } = useSelector((state: RootState) => state.careerPaths);
    const { skillGaps } = useSelector((state: RootState) => state.ml);
    const [selectedPath, setSelectedPath] = useState<CareerPath | null>(null);

    console.log({skillGaps});
    

    useEffect(() => {
        dispatch(fetchCareerPaths());
        dispatch(fetchUserCareerPaths());
    }, [dispatch]);

    useEffect(() => {
        // When user paths change, analyze skill gaps for each enrolled path
        userPaths.forEach(path => {
            dispatch(analyzeSkillGaps(path.id));
        });
    }, [userPaths, dispatch]);

    const handleJoinPath = async (pathId: number) => {
        try {
            await dispatch(joinCareerPath(pathId)).unwrap();
            dispatch(analyzeSkillGaps(pathId));
            setSelectedPath(careerPaths.find(p => p.id === pathId) || null);
        } catch (error) {
            console.error('Failed to join career path:', error);
        }
    };

    const handleLeavePath = async (pathId: number) => {
        if (window.confirm('Are you sure you want to leave this career path?')) {
            try {
                await dispatch(leaveCareerPath(pathId)).unwrap();
                setSelectedPath(null);
            } catch (error) {
                console.error('Failed to leave career path:', error);
            }
        }
    };

    const isUserEnrolled = (pathId: number) => {
        return userPaths.some(path => path.id === pathId);
    };

    const getSkillGapColor = (gap: number) => {
        if (gap === 0) return 'bg-green-500';
        if (gap <= 1) return 'bg-yellow-500';
        return 'bg-red-500';
    };

    const renderSkillProgress = (currentLevel: number, recommendedLevel: number) => {
        const percentage = Math.min((currentLevel / recommendedLevel) * 100, 100);
        return (
            <div className="w-full bg-gray-200 rounded-full h-2">
                <div
                    className="bg-blue-500 rounded-full h-2 transition-all duration-500"
                    style={{ width: `${percentage}%` }}
                />
            </div>
        );
    };

    return (
        <div className="container mx-auto px-4 py-8">
            {/* Career Paths Overview */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-blue-50 p-6 rounded-lg">
                    <div className="flex items-center">
                        <BriefcaseIcon className="h-8 w-8 text-blue-600" />
                        <div className="ml-4">
                            <h3 className="text-lg font-semibold text-blue-900">Available Paths</h3>
                            <p className="text-2xl font-bold text-blue-700">{careerPaths.length}</p>
                        </div>
                    </div>
                </div>
                <div className="bg-green-50 p-6 rounded-lg">
                    <div className="flex items-center">
                        <AcademicCapIcon className="h-8 w-8 text-green-600" />
                        <div className="ml-4">
                            <h3 className="text-lg font-semibold text-green-900">Your Paths</h3>
                            <p className="text-2xl font-bold text-green-700">{userPaths.length}</p>
                        </div>
                    </div>
                </div>
                <div className="bg-purple-50 p-6 rounded-lg">
                    <div className="flex items-center">
                        <ChartBarIcon className="h-8 w-8 text-purple-600" />
                        <div className="ml-4">
                            <h3 className="text-lg font-semibold text-purple-900">Skills Required</h3>
                            <p className="text-2xl font-bold text-purple-700">
                                {careerPaths?.reduce((total, path) => total + path?.required_skills?.length, 0)}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Career Paths List */}
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="px-6 py-4 border-b">
                    <h2 className="text-xl font-semibold">Career Paths</h2>
                </div>
                <div className="divide-y divide-gray-200">
                    {careerPaths.map((path) => (
                        <div key={path.id} className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-lg font-medium">{path.title}</h3>
                                <div>
                                    {isUserEnrolled(path.id) ? (
                                        <button
                                            onClick={() => handleLeavePath(path.id)}
                                            className="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                        >
                                            Leave Path
                                        </button>
                                    ) : (
                                        <button
                                            onClick={() => handleJoinPath(path.id)}
                                            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                        >
                                            Join Path
                                        </button>
                                    )}
                                </div>
                            </div>
                            <p className="text-gray-600 mb-4">{path.description}</p>
                            
                            {/* Required Skills */}
                            <div className="mt-4">
                                <h4 className="text-sm font-medium text-gray-700 mb-2">Required Skills</h4>
                                <div className="flex flex-wrap gap-2">
                                    {path?.required_skills?.map((skill) => (
                                        <span
                                            key={skill.id}
                                            className="px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded"
                                        >
                                            {skill.name} (Level {skill.importance_level})
                                        </span>
                                    ))}
                                </div>
                            </div>

                            {/* Skill Gaps Analysis */}
                            {isUserEnrolled(path.id) && skillGaps && (
                                <div className="mt-6 bg-gray-50 p-4 rounded-lg">
                                    <h4 className="text-sm font-medium text-gray-700 mb-4">Skills Analysis</h4>
                                    <div className="space-y-4">
                                        {skillGaps?.skill_gaps?.map((gap) => (
                                            <div key={gap.skill_id} className="bg-white p-4 rounded-lg shadow-sm">
                                                <div className="flex items-center justify-between mb-2">
                                                    <span className="font-medium text-gray-700">{gap.skill_name}</span>
                                                    <span className={`px-2 py-1 rounded text-white text-xs ${getSkillGapColor(gap.gap)}`}>
                                                        {gap.gap === 0 ? 'Achieved' : `Gap: ${gap.gap}`}
                                                    </span>
                                                </div>
                                                <div className="space-y-2">
                                                    {renderSkillProgress(gap.current_level, gap.recommended_level)}
                                                    <div className="flex justify-between text-xs text-gray-500">
                                                        <span>Current: {gap.current_level}</span>
                                                        <span>Target: {gap.recommended_level}</span>
                                                    </div>
                                                </div>
                                                {gap.gap > 0 && (
                                                    <div className="mt-3 text-sm">
                                                        <a href="#" className="text-blue-500 hover:text-blue-600">
                                                            Find courses to improve {gap.skill_name}
                                                        </a>
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default CareerPathsDashboard;
