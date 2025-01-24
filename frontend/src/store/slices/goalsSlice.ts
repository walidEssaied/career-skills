import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

interface Goal {
    id: number;
    title: string;
    description: string;
    status: 'not_started' | 'in_progress' | 'completed' | 'on_hold';
    priority: 'low' | 'medium' | 'high';
    progress: number;
    target_date: string;
    career_path_id: number;
    created_at: string;
    updated_at: string;
}

interface GoalsState {
    items: Goal[];
    loading: boolean;
    error: string | null;
}

const initialState: GoalsState = {
    items: [],
    loading: false,
    error: null,
};

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
            });
    },
});

export default goalsSlice.reducer;
