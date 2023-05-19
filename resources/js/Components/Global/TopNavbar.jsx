import Dropdown from '@/Components/Global/Dropdown';
import { Link } from '@inertiajs/inertia-react';
import HomeIcon from '@/Components/Icons/HomeIcon';

export default function TopNavbar({
  auth,
  showingNavigationDropdown,
  setShowingNavigationDropdown,
}) {
  return (
    <nav>
      <div className="px-4 py-2.5">
        <div className="flex justify-between">
          <div className="flex">
            <div className="hidden space-x-8 md:flex">
              <Link href={route('dashboard')} aria-label="Dashboard Link">
                <HomeIcon />
              </Link>
            </div>
          </div>

          <div className="flex items-center">
            <Dropdown>
              <Dropdown.Trigger>
                <span className="inline-flex rounded-md cursor-pointer text-slate-600 hover:text-slate-800">
                  {auth.user.name}

                  <svg
                    className="ml-1 mt-1 mr-0.5 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fillRule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clipRule="evenodd"
                    />
                  </svg>
                </span>
              </Dropdown.Trigger>

              <Dropdown.Content>
                <Dropdown.Link href={route('profile.edit')} method="get" as="button">
                  Profile
                </Dropdown.Link>
                <Dropdown.Link href={route('logout')} method="post" as="button">
                  Log Out
                </Dropdown.Link>
              </Dropdown.Content>
            </Dropdown>

            <button
              onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
              className="md:hidden ml-4 inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out"
            >
              <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  className={showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
}
