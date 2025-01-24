import { Dialog, Transition } from '@headlessui/react';
import {
    AcademicCapIcon,
    Bars3Icon,
    BriefcaseIcon,
    ChartBarIcon,
    HomeIcon,
    UserCircleIcon,
    UserGroupIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import React, { Fragment, useState } from 'react';
import { Link, Outlet, useLocation } from 'react-router-dom';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
    { name: 'Goals', href: '/goals', icon: ChartBarIcon },
    { name: 'Courses', href: '/courses', icon: AcademicCapIcon },
    { name: 'Career Paths', href: '/career-paths', icon: BriefcaseIcon },
    { name: 'Networking', href: '/networking', icon: UserGroupIcon },
];

const Layout: React.FC = () => {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const location = useLocation();

    return (
        <div className="h-screen flex overflow-hidden bg-gray-100 border-2 w-[100vw]">
            {/* Mobile sidebar */}
            <Transition.Root show={sidebarOpen} as={Fragment}>
                <Dialog as="div" className="fixed inset-0 flex z-40 md:hidden" onClose={setSidebarOpen}>
                    <Transition.Child
                        as={Fragment}
                        enter="transition-opacity ease-linear duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="transition-opacity ease-linear duration-300"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="fixed inset-0 bg-gray-600 bg-opacity-75" />
                    </Transition.Child>
                    <Transition.Child
                        as={Fragment}
                        enter="transition ease-in-out duration-300 transform"
                        enterFrom="-translate-x-full"
                        enterTo="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leaveFrom="translate-x-0"
                        leaveTo="-translate-x-full"
                    >
                        <div className="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                            <div className="absolute top-0 right-0 -mr-12 pt-2">
                                <button
                                    type="button"
                                    className="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                    onClick={() => setSidebarOpen(false)}
                                >
                                    <span className="sr-only">Close sidebar</span>
                                    <XMarkIcon className="h-6 w-6 text-white" aria-hidden="true" />
                                </button>
                            </div>
                            <div className="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                                <div className="flex-shrink-0 flex items-center px-4">
                                    <img
                                        className="h-8 w-auto"
                                        src="/logo.svg"
                                        alt="Career Skills Tracker"
                                    />
                                </div>
                                <nav className="mt-5 px-2 space-y-1">
                                    {navigation.map((item) => (
                                        <Link
                                            key={item.name}
                                            to={item.href}
                                            className={`
                                                group flex items-center px-2 py-2 text-base font-medium rounded-md
                                                ${location.pathname === item.href
                                                    ? 'bg-primary-500 text-white'
                                                    : 'text-gray-600 hover:bg-primary-50 hover:text-primary-700'
                                                }
                                            `}
                                        >
                                            <item.icon
                                                className={`
                                                    mr-4 h-6 w-6
                                                    ${location.pathname === item.href
                                                        ? 'text-white'
                                                        : 'text-gray-400 group-hover:text-primary-700'
                                                    }
                                                `}
                                                aria-hidden="true"
                                            />
                                            {item.name}
                                        </Link>
                                    ))}
                                </nav>
                            </div>
                            <div className="flex-shrink-0 flex border-t border-gray-200 p-4">
                                <Link to="/profile" className="flex-shrink-0 group block">
                                    <div className="flex items-center">
                                        <div>
                                            <UserCircleIcon className="inline-block h-10 w-10 rounded-full text-gray-400" />
                                        </div>
                                        <div className="ml-3">
                                            <p className="text-base font-medium text-gray-700 group-hover:text-primary-700">
                                                Profile
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </Transition.Child>
                </Dialog>
            </Transition.Root>

            {/* Static sidebar for desktop */}
            <div className="hidden md:flex md:flex-shrink-0">
                <div className="flex flex-col w-64">
                    <div className="flex flex-col h-0 flex-1 border-r border-gray-200 bg-white">
                        <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                            <div className="flex items-center flex-shrink-0 px-4">
                                <img
                                    className="h-8 w-auto"
                                    src="/logo.svg"
                                    alt="Career Skills Tracker"
                                />
                            </div>
                            <nav className="mt-5 flex-1 px-2 space-y-1">
                                {navigation.map((item) => (
                                    <Link
                                        key={item.name}
                                        to={item.href}
                                        className={`
                                            group flex items-center px-2 py-2 text-sm font-medium rounded-md
                                            ${location.pathname === item.href
                                                ? 'bg-primary-500 text-white'
                                                : 'text-gray-600 hover:bg-primary-50 hover:text-primary-700'
                                            }
                                        `}
                                    >
                                        <item.icon
                                            className={`
                                                mr-3 h-6 w-6
                                                ${location.pathname === item.href
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-primary-700'
                                                }
                                            `}
                                            aria-hidden="true"
                                        />
                                        {item.name}
                                    </Link>
                                ))}
                            </nav>
                        </div>
                        <div className="flex-shrink-0 flex border-t border-gray-200 p-4">
                            <Link to="/profile" className="flex-shrink-0 w-full group block">
                                <div className="flex items-center">
                                    <div>
                                        <UserCircleIcon className="inline-block h-9 w-9 rounded-full text-gray-400" />
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-sm font-medium text-gray-700 group-hover:text-primary-700">
                                            Profile
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main content */}
            <div className="flex flex-col w-0 flex-1 overflow-hidden">
                <div className="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
                    <button
                        type="button"
                        className="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <span className="sr-only">Open sidebar</span>
                        <Bars3Icon className="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <main className="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default Layout;
