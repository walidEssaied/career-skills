import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor for API calls
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for API calls
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Handle 401 Unauthorized response
        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            // Redirect to login page
            window.location.href = '/login';
            return Promise.reject(error);
        }

        return Promise.reject(error);
    }
);

export const endpoints = {
    // Auth endpoints
    login: '/login',
    register: '/register',
    logout: '/logout',
    
    // User endpoints
    profile: '/user/profile',
    updateProfile: '/user/profile/update',
    
    // Skills endpoints
    skills: '/skills',
    userSkills: '/user/skills',
    updateSkill: (skillId: number) => `/user/skills/${skillId}`,
    
    // Goals endpoints
    goals: '/goals',
    createGoal: '/goals',
    updateGoal: (goalId: number) => `/goals/${goalId}`,
    deleteGoal: (goalId: number) => `/goals/${goalId}`,
    goalStatistics: '/goals/statistics',
    
    // Course endpoints
    courses: '/courses',
    userCourses: '/user/courses',
    enrollCourse: (courseId: number) => `/courses/${courseId}/enroll`,
    completeCourse: (courseId: number) => `/courses/${courseId}/complete`,
    updateCourseProgress: (courseId: number) => `/courses/${courseId}/progress`,
    
    // Career Path endpoints
    careerPaths: '/career-paths',
    userCareerPaths: '/user/career-paths',
    joinCareerPath: (pathId: number) => `/career-paths/${pathId}/join`,
    leaveCareerPath: (pathId: number) => `/career-paths/${pathId}/leave`,
    careerPathDetails: (pathId: number) => `/career-paths/${pathId}`,
    
    // Networking endpoints
    connections: '/connections',
    connectionRequests: '/connections/requests',
    sendRequest: (userId: number) => `/connections/request/${userId}`,
    acceptRequest: (requestId: number) => `/connections/accept/${requestId}`,
    rejectRequest: (requestId: number) => `/connections/reject/${requestId}`,
    messages: '/messages',
    sendMessage: (userId: number) => `/messages/send/${userId}`,
    
    // ML endpoints
    mlRecommendations: '/ml/recommendations',
    mlCareerPrediction: '/ml/career-prediction',
    mlSkillGaps: '/ml/skill-gaps',
    mlProgressPrediction: '/ml/progress-prediction',
    
    // Recommendations
    recommendedSkills: '/recommendations/skills',
    recommendedCourses: '/recommendations/courses',
    recommendedPaths: '/recommendations/career-paths',
    
    // Mentors
    mentors: '/mentors',
    connectMentor: (mentorId: number) => `/mentors/${mentorId}/connect`,
};

export default api;
