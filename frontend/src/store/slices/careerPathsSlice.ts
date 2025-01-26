import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

export interface CareerPath {
    id: number;
    title: string;
    description: string;
    required_experience_years: number;
    required_skills: {
        id: number;
        name: string;
        importance_level: number;
    }[];
    created_at: string;
    updated_at: string;
}

interface CareerPathsState {
    items: CareerPath[];
    userPaths: CareerPath[];
    recommendations: CareerPath[];
    loading: boolean;
    error: string | null;
}

const initialState: CareerPathsState = {
    items: [],
    userPaths: [],
    recommendations: [],
    loading: false,
    error: null,
};

// Async Thunks
export const fetchCareerPaths = createAsyncThunk(
    'careerPaths/fetchAll',
    async () => {
        const response = await api.get(endpoints.careerPaths);
        return response.data;
    }
);

export const fetchUserCareerPaths = createAsyncThunk(
    'careerPaths/fetchUserPaths',
    async () => {
        const response = await api.get(endpoints.userCareerPaths);
        return response.data;
    }
);

export const joinCareerPath = createAsyncThunk(
    'careerPaths/join',
    async (pathId: number) => {
        const response = await api.post(`${endpoints.userCareerPaths}/${pathId}/join`);
        return response.data;
    }
);

export const leaveCareerPath = createAsyncThunk(
    'careerPaths/leave',
    async (pathId: number) => {
        await api.delete(`${endpoints.userCareerPaths}/${pathId}/leave`);
        return pathId;
    }
);

export const getCareerPathRecommendations = createAsyncThunk(
    'careerPaths/getRecommendations',
    async () => {
        const response = await api.get(endpoints.mlRecommendations);
        return response.data;
    }
);

const careerPathsSlice = createSlice({
    name: 'careerPaths',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            // Fetch All Career Paths
            .addCase(fetchCareerPaths.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchCareerPaths.fulfilled, (state, action) => {
                state.loading = false;
                state.items = action.payload;
            })
            .addCase(fetchCareerPaths.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch career paths';
            })
            // Fetch User Career Paths
            .addCase(fetchUserCareerPaths.fulfilled, (state, action) => {
                state.userPaths = action.payload;
            })
            // Join Career Path
            .addCase(joinCareerPath.fulfilled, (state, action) => {
                state.userPaths.push(action.payload);
            })
            // Leave Career Path
            .addCase(leaveCareerPath.fulfilled, (state, action) => {
                state.userPaths = state.userPaths.filter(path => path.id !== action.payload);
            })
            // Get Recommendations
            .addCase(getCareerPathRecommendations.fulfilled, (state, action) => {
                state.recommendations = action.payload;
            });
    },
});

export default careerPathsSlice.reducer;
