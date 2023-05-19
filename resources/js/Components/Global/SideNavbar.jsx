import ApplicationLogo from '@/Components/Global/ApplicationLogo';
import NavLink from '@/Components/Global/NavLink';
import { sidebarAtom } from '@/GlobalStates/atoms';
import { Link } from '@inertiajs/inertia-react';
import { useAtom } from 'jotai';
import { useEffect } from 'react';

export default function SideNavbar({ showingNavigationDropdown }) {
  const [showingSideNavigationDropdown, setShowingSideNavigationDropdown] = useAtom(sidebarAtom);
  let toggle = () => {
    setShowingSideNavigationDropdown(
      (prevShowingSideNavigationDropdown) => !prevShowingSideNavigationDropdown
    );
  };
  useEffect(() => {
    if (
      route().current('users.*') ||
      route().current('stations.*') ||
      route().current('qualifications.*') ||
      route().current('dispositions.*') ||
      route().current('states.*') ||
      route().current('toll-free-numbers.*') ||
      route().current('restricted-ani.*') ||
      route().current('activity-log')
    ) {
      setShowingSideNavigationDropdown(true);
    }
  }, []);
  return (
    <aside
      className={
        (showingNavigationDropdown ? 'block' : 'hidden') +
        ' md:block fixed lg:fixed md:fixed top-0 left-0 w-64 flex-shrink-0 h-screen bg-white z-10 shadow md:shadow-none'
      }
    >
      <div className="px-4 py-3 shrink-0 flex items-center h-14">
        <Link href={route('dashboard')}>
          <ApplicationLogo className="text-slate-600 hover:text-slate-700" />
        </Link>
      </div>
      <nav className="px-4 h-[calc(100vh-54px)] overflow-auto pt-1">
        <NavLink href={route('offers.index')} active={route().current('offers.*')}>
          Offer Setup
        </NavLink>
        <NavLink
          href={route('offerTollFreeNumbers.index')}
          active={route().current('offerTollFreeNumbers*')}
        >
          TFN Assignments
        </NavLink>
        <NavLink href={route('providers.index')} active={route().current('providers.*')}>
          Providers
        </NavLink>
        <NavLink href={route('provider-files.index')} active={route().current('provider-files.*')}>
          Providers Files
        </NavLink>
        <NavLink href={route('reports.index')} active={route().current('reports.*')}>
          Data File Report
        </NavLink>
        <NavLink
          href={route('all_reports.report_form')}
          active={route().current('all_reports.report_form')}
        >
          Generate Report
        </NavLink>

        <div
          className="mt-4 mb-1 py-2 px-4 font-semibold bg-slate-100 text-slate-600 rounded-md cursor-pointer"
          onClick={() => toggle()}
        >
          Options
          <span className="float-right">
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
        </div>
        {showingSideNavigationDropdown && (
          <div>
            <NavLink href={route('users.index')} active={route().current('users.*')}>
              Clients
            </NavLink>
            <NavLink href={route('stations.index')} active={route().current('stations.*')}>
              Stations
            </NavLink>
            <NavLink
              href={route('qualifications.index')}
              active={route().current('qualifications.*')}
            >
              Qualifications
            </NavLink>
            <NavLink href={route('dispositions.index')} active={route().current('dispositions.*')}>
              Dispositions
            </NavLink>
            <NavLink href={route('states.index')} active={route().current('states.*')}>
              Restricted States
            </NavLink>
            <NavLink
              href={route('tollFreeNumbers.index')}
              active={route().current('tollFreeNumbers.*')}
            >
              Toll Free Numbers
            </NavLink>
            <NavLink
              href={route('restricted-ani.index')}
              active={route().current('restricted-ani.*')}
            >
              Restricted Ani
            </NavLink>
            <NavLink
              href={route('zipcodeByStations.index')}
              active={route().current('zipcodeByStations.*')}
            >
              Zipcode By State
            </NavLink>
            <NavLink href={route('activity-log')} active={route().current('activity-log')}>
              Activity Log
            </NavLink>
          </div>
        )}
      </nav>
    </aside>
  );
}
