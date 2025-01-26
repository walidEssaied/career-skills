import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    getCourseRecommendations,
    predictCareerPath,
    analyzeSkillGaps,
} from '../../store/slices/mlSlice';
import {
    AcademicCapIcon,
    BriefcaseIcon,
    ChartBarIcon,
    LightBulbIcon,
    ArrowTrendingUpIcon,
} from '@heroicons/react/24/outline';

const MLDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { courseRecommendations, careerPredictions, skillGaps, loading } = useSelector(
        (state: RootState) => state.ml
    );
    const [selectedCareerPath, setSelectedCareerPath] = useState<number | null>(null);

    useEffect(() => {
        dispatch(getCourseRecommendations());
        dispatch(predictCareerPath());
    }, [dispatch]);

    useEffect(() => {
        if (selectedCareerPath !== null) {
            dispatch(analyzeSkillGaps(selectedCareerPath));
        }
    }, [selectedCareerPath, dispatch]);

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
        );
    }

    const getConfidenceColor = (confidence: number) => {
        if (confidence >= 80) return 'bg-green-100 text-green-800';
        if (confidence >= 60) return 'bg-blue-100 text-blue-800';
        if (confidence >= 40) return 'bg-yellow-100 text-yellow-800';
        return 'bg-gray-100 text-gray-800';
    };

    const getSkillLevelColor = (current: number, required: number) => {
        const ratio = current / required;
        if (ratio >= 1) return 'bg-green-100 text-green-800';
        if (ratio >= 0.7) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    };

    return (
        <div className="container mx-auto px-4 py-8">
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-gray-900">AI Insights</h1>
                <p className="mt-2 text-gray-600">
                    Personalized recommendations and predictions based on your profile and progress
                </p>
            </div>

            {/* ML Insights Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {/* Course Recommendations */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-200 flex items-center">
                        <AcademicCapIcon className="h-6 w-6 text-blue-500 mr-2" />
                        <h2 className="text-xl font-semibold text-black">Recommended Courses</h2>
                    </div>
                    <div className="p-6">
                        <div className="space-y-4">
                            {courseRecommendations.map((course: any) => (
                                <div
                                    key={course.id}
                                    className="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div className="flex-1">
                                        <div className="flex justify-between items-start">
                                            <h3 className="font-medium text-gray-900">
                                                {course.title}
                                            </h3>
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getConfidenceColor(course.match_score)}`}>
                                                {Math.round(course.match_score)}% Match
                                            </span>
                                        </div>
                                        <p className="mt-1 text-sm text-gray-500">
                                            {course.description}
                                        </p>
                                        <div className="mt-2 grid grid-cols-2 gap-2">
                                            <div className="text-sm text-gray-500">
                                                <span className="font-medium">Provider:</span> {course.provider}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                <span className="font-medium">Duration:</span> {course.duration}
                                            </div>
                                        </div>
                                        {course.skills && course.skills.length > 0 && (
                                            <div className="mt-3">
                                                <h4 className="text-sm font-medium text-gray-700 mb-2">Skills You'll Learn:</h4>
                                                <div className="flex flex-wrap gap-2">
                                                    {course.skills.map((skill: any) => (
                                                        <span
                                                            key={skill.id}
                                                            className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                        >
                                                            {skill.name} (Level {skill.level_gained})
                                                        </span>
                                                    ))}
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Career Path Predictions */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-200 flex items-center">
                        <BriefcaseIcon className="h-6 w-6 text-green-500 mr-2" />
                        <h2 className="text-xl font-semibold text-black">Career Path Predictions</h2>
                    </div>
                    <div className="p-6">
                        <div className="space-y-6">
                            {careerPredictions.map((prediction: any) => (
                                <div
                                    key={prediction.path_id}
                                    className={`p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer ${selectedCareerPath === prediction.path_id ? 'ring-2 ring-blue-500' : ''}`}
                                    onClick={() => setSelectedCareerPath(prediction.path_id)}
                                >
                                    <div className="flex items-center justify-between">
                                        <h3 className="font-medium text-gray-900">
                                            {prediction.title}
                                        </h3>
                                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getConfidenceColor(prediction.confidence)}`}>
                                            {Math.round(prediction.confidence)}% Match
                                        </span>
                                    </div>
                                    <p className="mt-1 text-sm text-gray-500">
                                        {prediction.description}
                                    </p>
                                    
                                    {/* Matching Skills */}
                                    {Object.values(prediction.matching_skills).length > 0 && (
                                        <div className="mt-4">
                                            <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                Matching Skills
                                            </h4>
                                            <div className="space-y-2">
                                                {Object.values(prediction.matching_skills).map((skill: any) => (
                                                    <div
                                                        key={skill.id}
                                                        className="flex items-center justify-between p-2 bg-white rounded border border-gray-200"
                                                    >
                                                        <span className="text-sm font-medium text-gray-700">
                                                            {skill.name}
                                                        </span>
                                                        <div className="flex items-center space-x-2">
                                                            <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getSkillLevelColor(skill.current_level, skill.required_level)}`}>
                                                                Current: {skill.current_level}
                                                            </span>
                                                            <ArrowTrendingUpIcon className="h-4 w-4 text-gray-400" />
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                Required: {skill.required_level}
                                                            </span>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* Skill Gaps Analysis */}
                                    {selectedCareerPath === prediction.path_id && skillGaps && skillGaps.length > 0 && (
                                        <div className="mt-6">
                                            <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                Detailed Skill Gap Analysis
                                            </h4>
                                            <div className="space-y-2">
                                                {skillGaps.map((gap: any) => (
                                                    <div
                                                        key={gap.skill_id}
                                                        className="p-2 bg-white rounded border border-gray-200"
                                                    >
                                                        <div className="flex justify-between items-center mb-1">
                                                            <span className="text-sm font-medium text-gray-700">
                                                                {gap.skill_name}
                                                            </span>
                                                            <span className="text-xs text-gray-500">
                                                                Importance: {gap.importance}
                                                            </span>
                                                        </div>
                                                        <div className="relative pt-1">
                                                            <div className="flex mb-2 items-center justify-between">
                                                                <div>
                                                                    <span className="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                                                        Progress
                                                                    </span>
                                                                </div>
                                                                <div className="text-right">
                                                                    <span className="text-xs font-semibold inline-block text-blue-600">
                                                                        {Math.round((gap.current_level / gap.recommended_level) * 100)}%
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div className="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                                                                <div
                                                                    style={{ width: `${Math.min(100, (gap.current_level / gap.recommended_level) * 100)}%` }}
                                                                    className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
                                                                ></div>
                                                            </div>
                                                        </div>
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
            </div>
        </div>
    );
};

export default MLDashboard;
