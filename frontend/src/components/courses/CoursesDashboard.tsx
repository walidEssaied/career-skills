import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    Course,
    fetchCourses,
    fetchRecommendedCourses,
    updateCourseProgress,
    rateCourse,
} from '../../store/slices/coursesSlice';
import { StarIcon } from '@heroicons/react/24/solid';
import { StarIcon as StarOutlineIcon } from '@heroicons/react/24/outline';

const CoursesDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: courses, recommendedCourses, loading } = useSelector(
        (state: any) => state?.courses!
    );
    const [selectedFilter, setSelectedFilter] = useState<string>('all');
    const [searchQuery, setSearchQuery] = useState<string>('');

    useEffect(() => {
        dispatch(fetchCourses());
        dispatch(fetchRecommendedCourses());
    }, [dispatch]);

    const filteredCourses = courses.filter((course: any) => {
        const matchesSearch = course.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
            course.description.toLowerCase().includes(searchQuery.toLowerCase());
        
        if (selectedFilter === 'all') return matchesSearch;
        if (selectedFilter === 'in_progress') return course.completion_status === 'in_progress' && matchesSearch;
        if (selectedFilter === 'completed') return course.completion_status === 'completed' && matchesSearch;
        if (selectedFilter === 'not_started') return course.completion_status === 'not_started' && matchesSearch;
        return true;
    });

    const handleProgressUpdate = (courseId: number, progress: number) => {
        dispatch(updateCourseProgress({ courseId, progress }));
    };

    const handleRating = (courseId: number, rating: number) => {
        dispatch(rateCourse({ courseId, rating }));
    };

    const renderStarRating = (rating: number = 0, courseId: number) => {
        return (
            <div className="flex items-center">
                {[1, 2, 3, 4, 5].map((star) => (
                    <button
                        key={star}
                        onClick={() => handleRating(courseId, star)}
                        className="focus:outline-none"
                    >
                        {star <= rating ? (
                            <StarIcon className="h-5 w-5 text-yellow-400" />
                        ) : (
                            <StarOutlineIcon className="h-5 w-5 text-yellow-400" />
                        )}
                    </button>
                ))}
            </div>
        );
    };

    const renderCourseCard = (course: Course) => (
        <div key={course.id} className="bg-white rounded-lg shadow-md p-6">
            <div className="flex justify-between items-start">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900">{course.title}</h3>
                    <p className="text-sm text-gray-500">{course.provider}</p>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    course.difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' :
                    course.difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                }`}>
                    {course.difficulty_level}
                </span>
            </div>
            
            <p className="mt-2 text-sm text-gray-600">{course.description}</p>
            
            <div className="mt-4">
                <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-gray-700">Progress</span>
                    <span className="text-sm text-gray-500">{course.progress}%</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2">
                    <div
                        className="bg-primary-600 h-2 rounded-full"
                        style={{ width: `${course.progress}%` }}
                    />
                </div>
            </div>

            <div className="mt-4 space-y-2">
                <div className="flex justify-between items-center">
                    <span className="text-sm text-gray-500">Duration: {course.duration}</span>
                    {course.price && (
                        <span className="text-sm font-medium text-gray-900">
                            {course.price} {course.currency}
                        </span>
                    )}
                </div>
                <div className="flex justify-between items-center">
                    <div className="flex items-center space-x-1">
                        {renderStarRating(course.rating, course.id)}
                        <span className="text-sm text-gray-500">({course.reviews_count})</span>
                    </div>
                    {course.certificate_offered && (
                        <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            Certificate
                        </span>
                    )}
                </div>
            </div>

            <div className="mt-4 flex justify-end">
                <a
                    href={course.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary-600 hover:text-primary-700 text-sm font-medium"
                >
                    Go to course →
                </a>
            </div>
        </div>
    );

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>
        );
    }

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div className="md:flex md:items-center md:justify-between">
                <div className="flex-1 min-w-0">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Learning Courses
                    </h2>
                </div>
            </div>

            <div className="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div className="flex items-center space-x-4">
                    <select
                        value={selectedFilter}
                        onChange={(e) => setSelectedFilter(e.target.value)}
                        className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                    >
                        <option value="all">All Courses</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="not_started">Not Started</option>
                    </select>
                    <input
                        type="text"
                        placeholder="Search courses..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    />
                </div>
            </div>

            {recommendedCourses.length > 0 && (
                <div className="mt-8">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Recommended for You</h3>
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {recommendedCourses.map(renderCourseCard)}
                    </div>
                </div>
            )}

            <div className="mt-8">
                <h3 className="text-lg font-medium text-gray-900 mb-4">All Courses</h3>
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {filteredCourses.map(renderCourseCard)}
                </div>
            </div>
        </div>
    );
};

export default CoursesDashboard;
