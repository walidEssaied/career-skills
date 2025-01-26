import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

export interface Goal {
    id: number;
    title: string;
    description: string;
    status: 'not_started' | 'in_progress' | 'completed' | 'on_hold';
    priority: 'low' | 'medium' | 'high';
    progress: number;
    target_date: string;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface GoalStatistics {
    total_goals: number;
    completed_goals: number;
    in_progress_goals: number;
    completion_rate: number;
}

interface GoalsState {
    items: Goal[];
    statistics: GoalStatistics | null;
    loading: boolean;
    error: string | null;
}

const initialState: GoalsState = {
    items: [],
    statistics: null,
    loading: false,
    error: null,
};

// Async Thunks
export const fetchGoals = createAsyncThunk(
    'goals/fetchGoals',
    async () => {
        const response = await api.get(endpoints.goals);
        return response.data;
    }
);

export const createGoal = createAsyncThunk(
    'goals/createGoal',
    async (goalData: Partial<Goal>) => {
        const response = await api.post(endpoints.goals, goalData);
        return response.data;
    }
);

export const updateGoal = createAsyncThunk(
    'goals/updateGoal',
    async ({ id, data }: { id: number; data: Partial<Goal> }) => {
        const response = await api.put(`${endpoints.goals}/${id}`, data);
        return response.data;
    }
);

export const deleteGoal = createAsyncThunk(
    'goals/deleteGoal',
    async (id: number) => {
        await api.delete(`${endpoints.goals}/${id}`);
        return id;
    }
);

export const updateGoalProgress = createAsyncThunk(
    'goals/updateProgress',
    async ({ id, progress }: { id: number; progress: number }) => {
        const response = await api.put(`${endpoints.goals}/${id}/progress`, { progress });
        return response.data;
    }
);

export const fetchGoalStatistics = createAsyncThunk(
    'goals/fetchStatistics',
    async () => {
        const response = await api.get(endpoints.goalStatistics);
        return response.data;
    }
);

const goalsSlice = createSlice({
    name: 'goals',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            // Fetch Goals
            .addCase(fetchGoals.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchGoals.fulfilled, (state, action) => {
                state.loading = false;
                state.items = action.payload;
            })
            .addCase(fetchGoals.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch goals';
            })
            // Create Goal
            .addCase(createGoal.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(createGoal.fulfilled, (state, action) => {
                state.loading = false;
                state.items.push(action.payload);
            })
            .addCase(createGoal.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to create goal';
            })
            // Update Goal
            .addCase(updateGoal.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(updateGoal.fulfilled, (state, action) => {
                state.loading = false;
                const index = state.items.findIndex(goal => goal.id === action.payload.id);
                if (index !== -1) {
                    state.items[index] = action.payload;
                }
            })
            .addCase(updateGoal.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to update goal';
            })
            // Delete Goal
            .addCase(deleteGoal.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(deleteGoal.fulfilled, (state, action) => {
                state.loading = false;
                state.items = state.items.filter(goal => goal.id !== action.payload);
            })
            .addCase(deleteGoal.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to delete goal';
            })
            // Update Progress
            .addCase(updateGoalProgress.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(updateGoalProgress.fulfilled, (state, action) => {
                state.loading = false;
                const index = state.items.findIndex(goal => goal.id === action.payload.id);
                if (index !== -1) {
                    state.items[index] = action.payload;
                }
            })
            .addCase(updateGoalProgress.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to update progress';
            })
            // Fetch Statistics
            .addCase(fetchGoalStatistics.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchGoalStatistics.fulfilled, (state, action) => {
                state.loading = false;
                state.statistics = action.payload;
            })
            .addCase(fetchGoalStatistics.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch statistics';
            });
    }
});

export default goalsSlice.reducer;
