import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import {
    HomeIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    UserGroupIcon,
    ChartBarIcon,
    FlagIcon,
} from '@heroicons/react/24/outline';

const Navigation: React.FC = () => {
    const location = useLocation();
    const { user } = useSelector((state: RootState) => state.auth);

    const navigation = [
        { name: 'Goals', href: '/goals', icon: FlagIcon },
        { name: 'Courses', href: '/courses', icon: AcademicCapIcon },
        { name: 'Career Paths', href: '/career-paths', icon: BriefcaseIcon },
        { name: 'Networking', href: '/networking', icon: UserGroupIcon },
        { name: 'ML Insights', href: '/ml-insights', icon: ChartBarIcon },
    ];

    const isActive = (path: string) => location.pathname === path;

    return (
        <nav className="bg-white shadow">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    <div className="flex">
                        <div className="flex-shrink-0 flex items-center">
                            <Link to="/" className="text-xl font-bold text-primary-600">
                                Career Skills
                            </Link>
                        </div>
                        <div className="hidden sm:ml-6 sm:flex sm:space-x-8">
                            {navigation.map((item) => {
                                const Icon = item.icon;
                                return (
                                    <Link
                                        key={item.name}
                                        to={item.href}
                                        className={`${
                                            isActive(item.href)
                                                ? 'border-primary-500 text-gray-900'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                        } inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium`}
                                    >
                                        <Icon className="h-5 w-5 mr-1" />
                                        {item.name}
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                    <div className="hidden sm:ml-6 sm:flex sm:items-center">
                        <div className="ml-3 relative">
                            <div className="flex items-center">
                                <span className="text-sm text-gray-700 mr-2">
                                    {user?.name}
                                </span>
                                <img
                                    className="h-8 w-8 rounded-full"
                                    src={user?.avatar || 'https://ui-avatars.com/api/?name=' + user?.name}
                                    alt={user?.name}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    );
};

export default Navigation;
