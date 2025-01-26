import { StarIcon as StarOutlineIcon } from '@heroicons/react/24/outline';
import { StarIcon } from '@heroicons/react/24/solid';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    Course,
    fetchCourses,
    fetchRecommendedCourses,
    rateCourse,
    updateCourseProgress,
} from '../../store/slices/coursesSlice';

const CoursesDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: courses, recommendedCourses, loading, error } = useSelector(
        (state: RootState) => state.courses
    );
    const [selectedFilter, setSelectedFilter] = useState<string>('all');
    const [searchQuery, setSearchQuery] = useState<string>('');

    useEffect(() => {
        dispatch(fetchCourses());
        dispatch(fetchRecommendedCourses());
    }, [dispatch]);

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="text-center py-12">
                <div className="text-red-500 mb-4">Error loading courses: {error}</div>
                <button
                    onClick={() => {
                        dispatch(fetchCourses());
                        dispatch(fetchRecommendedCourses());
                    }}
                    className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Retry
                </button>
            </div>
        );
    }

    const filteredCourses = (courses || []).filter((course: Course) => {
        const matchesSearch = course.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
            course.description.toLowerCase().includes(searchQuery.toLowerCase());
        
        if (selectedFilter === 'all') return matchesSearch;
        if (selectedFilter === 'in_progress') return matchesSearch && course.completion_status === 'in_progress';
        if (selectedFilter === 'completed') return matchesSearch && course.completion_status === 'completed';
        return matchesSearch && course.completion_status === 'not_started';
    });

    const handleRating = (courseId: number, rating: number) => {
        dispatch(rateCourse({ courseId, rating }));
    };

    const handleProgressUpdate = (courseId: number, progress: number) => {
        dispatch(updateCourseProgress({ courseId, progress }));
    };

    return (
        <div className="space-y-8">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div className="flex flex-wrap gap-2">
                    <button
                        onClick={() => setSelectedFilter('all')}
                        className={`px-4 py-2 rounded-lg ${
                            selectedFilter === 'all'
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        All Courses
                    </button>
                    <button
                        onClick={() => setSelectedFilter('in_progress')}
                        className={`px-4 py-2 rounded-lg ${
                            selectedFilter === 'in_progress'
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        In Progress
                    </button>
                    <button
                        onClick={() => setSelectedFilter('completed')}
                        className={`px-4 py-2 rounded-lg ${
                            selectedFilter === 'completed'
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        Completed
                    </button>
                </div>
                <input
                    type="text"
                    placeholder="Search courses..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="w-full sm:w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            {recommendedCourses && recommendedCourses.length > 0 && (
                <div>
                    <h2 className="text-2xl font-bold mb-4 text-black">Recommended Courses</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {recommendedCourses.map((course) => (
                            <CourseCard
                                key={course.id}
                                course={course}
                                onRate={handleRating}
                                onProgressUpdate={handleProgressUpdate}
                            />
                        ))}
                    </div>
                </div>
            )}

            <div>
                <h2 className="text-2xl font-bold mb-4">All Courses</h2>
                {filteredCourses.length === 0 ? (
                    <div className="text-center py-12 bg-gray-50 rounded-lg">
                        <p className="text-gray-500">No courses found matching your criteria.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {filteredCourses.map((course) => (
                            <CourseCard
                                key={course.id}
                                course={course}
                                onRate={handleRating}
                                onProgressUpdate={handleProgressUpdate}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
};

interface CourseCardProps {
    course: Course;
    onRate: (courseId: number, rating: number) => void;
    onProgressUpdate: (courseId: number, progress: number) => void;
}

const CourseCard: React.FC<CourseCardProps> = ({ course, onRate, onProgressUpdate }) => {
    return (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="p-6">
                <h3 className="text-lg font-semibold mb-2 text-black">{course.title}</h3>
                <p className="text-gray-600 mb-4 line-clamp-2 ">{course.description}</p>
                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center">
                        {[1, 2, 3, 4, 5].map((star) => (
                            <button
                                key={star}
                                onClick={() => onRate(course.id, star)}
                                className="text-yellow-400"
                            >
                                {star <= (course.rating || 0) ? (
                                    <StarIcon className="h-5 w-5" />
                                ) : (
                                    <StarOutlineIcon className="h-5 w-5" />
                                )}
                            </button>
                        ))}
                        <span className="ml-2 text-sm text-gray-500">
                            ({course.reviews_count} reviews)
                        </span>
                    </div>
                    <span className="text-sm font-medium text-gray-900">
                        {course.price ? `${course.currency}${course.price}` : 'Free'}
                    </span>
                </div>
                <div className="space-y-4">
                    <div>
                        <div className="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Progress</span>
                            <span>{course.progress}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                            <div
                                className="bg-blue-500 h-2 rounded-full"
                                style={{ width: `${course.progress}%` }}
                            ></div>
                        </div>
                    </div>
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-500">{course.duration}</span>
                        <span
                            className={`px-2 py-1 rounded ${
                                course.difficulty_level === 'beginner'
                                    ? 'bg-green-100 text-green-800'
                                    : course.difficulty_level === 'intermediate'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-red-100 text-red-800'
                            }`}
                        >
                            {course.difficulty_level}
                        </span>
                    </div>
                    <button
                        onClick={() => {
                            const newProgress = course.progress >= 100 ? 0 : course.progress + 25;
                            onProgressUpdate(course.id, newProgress);
                        }}
                        className="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        {course.progress >= 100 ? 'Restart Course' : 'Continue Learning'}
                    </button>
                </div>
            </div>
        </div>
    );
};

export default CoursesDashboard;
