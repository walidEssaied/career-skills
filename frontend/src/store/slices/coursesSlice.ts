import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

export interface Course {
    id: number;
    title: string;
    description: string;
    provider: string;
    url: string;
    duration: string;
    difficulty_level: 'beginner' | 'intermediate' | 'advanced';
    skills_covered: string[];
    completion_status: 'not_started' | 'in_progress' | 'completed';
    progress: number;
    rating?: number;
    reviews_count: number;
    price?: number;
    currency?: string;
    certificate_offered: boolean;
}

interface CoursesState {
    items: Course[];
    recommendedCourses: Course[];
    loading: boolean;
    error: string | null;
}

const initialState: CoursesState = {
    items: [],
    recommendedCourses: [],
    loading: false,
    error: null,
};

export const fetchCourses = createAsyncThunk(
    'courses/fetchCourses',
    async () => {
        const response = await api.get(endpoints.courses);
        return response.data;
    }
);

export const fetchRecommendedCourses = createAsyncThunk(
    'courses/fetchRecommendedCourses',
    async () => {
        const response = await api.get(endpoints.recommendedCourses);
        return response.data;
    }
);

export const updateCourseProgress = createAsyncThunk(
    'courses/updateProgress',
    async ({ courseId, progress }: { courseId: number; progress: number }) => {
        const response = await api.put(`${endpoints.courses}/${courseId}/progress`, { progress });
        return response.data;
    }
);

export const rateCourse = createAsyncThunk(
    'courses/rateCourse',
    async ({ courseId, rating }: { courseId: number; rating: number }) => {
        const response = await api.post(`${endpoints.courses}/${courseId}/rate`, { rating });
        return response.data;
    }
);

const coursesSlice = createSlice({
    name: 'courses',
    initialState,
    reducers: {
        resetCoursesError: (state) => {
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        builder
            // Fetch courses
            .addCase(fetchCourses.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchCourses.fulfilled, (state, action) => {
                state.loading = false;
                state.items = action.payload;
            })
            .addCase(fetchCourses.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch courses';
            })
            // Fetch recommended courses
            .addCase(fetchRecommendedCourses.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchRecommendedCourses.fulfilled, (state, action) => {
                state.loading = false;
                state.recommendedCourses = action.payload;
            })
            .addCase(fetchRecommendedCourses.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch recommended courses';
            })
            // Update course progress
            .addCase(updateCourseProgress.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(updateCourseProgress.fulfilled, (state, action) => {
                state.loading = false;
                const updatedCourse = action.payload;
                const index = state.items.findIndex(course => course.id === updatedCourse.id);
                if (index !== -1) {
                    state.items[index] = updatedCourse;
                }
            })
            .addCase(updateCourseProgress.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to update course progress';
            })
            // Rate course
            .addCase(rateCourse.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(rateCourse.fulfilled, (state, action) => {
                state.loading = false;
                const updatedCourse = action.payload;
                const index = state.items.findIndex(course => course.id === updatedCourse.id);
                if (index !== -1) {
                    state.items[index] = updatedCourse;
                }
            })
            .addCase(rateCourse.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to rate course';
            });
    },
});

export const { resetCoursesError } = coursesSlice.actions;
export default coursesSlice.reducer;
