import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

interface SkillGap {
    skill_id: number;
    skill_name: string;
    current_level: number;
    recommended_level: number;
    importance: number;
}

interface MLState {
    courseRecommendations: any[];
    careerPredictions: any[];
    skillGaps: {
        skill_gaps: SkillGap[];
        career_path: {
            id: number;
            title: string;
        };
    }[];
    loading: boolean;
    error: string | null;
}

const initialState: MLState = {
    courseRecommendations: [],
    careerPredictions: [],
    skillGaps: [],
    loading: false,
    error: null,
};

export const getCourseRecommendations = createAsyncThunk(
    'ml/getCourseRecommendations',
    async () => {
        const response = await api.get(endpoints.mlRecommendations);
        return response.data;
    }
);

export const predictCareerPath = createAsyncThunk(
    'ml/predictCareerPath',
    async () => {
        const response = await api.get(endpoints.mlCareerPrediction);
        return response.data;
    }
);

export const analyzeSkillGaps = createAsyncThunk(
    'ml/analyzeSkillGaps',
    async (careerPathId: number) => {
        const response = await api.post(endpoints.mlSkillGaps, { career_path_id: careerPathId });
        return response.data;
    }
);

const mlSlice = createSlice({
    name: 'ml',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            // Course Recommendations
            .addCase(getCourseRecommendations.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(getCourseRecommendations.fulfilled, (state, action) => {
                state.loading = false;
                state.courseRecommendations = action.payload;
            })
            .addCase(getCourseRecommendations.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to get recommendations';
            })
            // Career Predictions
            .addCase(predictCareerPath.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(predictCareerPath.fulfilled, (state, action) => {
                state.loading = false;
                state.careerPredictions = action.payload;
            })
            .addCase(predictCareerPath.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to predict career path';
            })
            // Skill Gaps Analysis
            .addCase(analyzeSkillGaps.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(analyzeSkillGaps.fulfilled, (state, action) => {
                state.loading = false;
                state.skillGaps = action.payload;
            })
            .addCase(analyzeSkillGaps.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to analyze skill gaps';
            });
    },
});

export default mlSlice.reducer;
