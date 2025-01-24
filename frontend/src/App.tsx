import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { Provider } from 'react-redux';
import { store } from './store';
import SkillsDashboard from './components/skills/SkillsDashboard';
import PrivateRoute from './components/auth/PrivateRoute';
import Login from './components/auth/Login';
import Register from './components/auth/Register';
import Profile from './components/profile/Profile';
import GoalsDashboard from './components/goals/GoalsDashboard';
import Layout from './components/layout/Layout';
import CoursesDashboard from './components/courses/CoursesDashboard';

function App() {
  return (
    <Provider store={store}>
      <Router>
        <Routes>
          {/* Public Routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />

          {/* Protected Routes */}
          <Route element={<PrivateRoute><Layout /></PrivateRoute>}>
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="/dashboard" element={<SkillsDashboard />} />
            <Route path="/profile" element={<Profile />} />
            <Route path="/goals" element={<GoalsDashboard />} />
            <Route path="/courses" element={<CoursesDashboard />} />
            {/* <Route path="/career-paths" element={<CareerPathsDashboard />} /> */}
            {/* <Route path="/networking" element={<NetworkingDashboard />} /> */}
          </Route>
        </Routes>
      </Router>
    </Provider>
  );
}

export default App;
