import React, { useEffect, useState } from 'react';
import SideNavbar from '@/Components/Global/SideNavbar';
import TopNavbar from '@/Components/Global/TopNavbar';

export default function Authenticated({ auth, children, className = '' }) {
  const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

  return (
    <div className="flex flex-row">
      <SideNavbar showingNavigationDropdown={showingNavigationDropdown} />
      <section className="w-full lg:absolute lg:left-64 md:absolute md:left-64 md:w-[calc(100%-16rem)]">
        <TopNavbar
          auth={auth}
          showingNavigationDropdown={showingNavigationDropdown}
          setShowingNavigationDropdown={setShowingNavigationDropdown}
        />
        <main>
          <div className={'mx-auto sm:px-4 pb-3 ' + className}>
            <div className="bg-white shadow-sm sm:rounded-lg">
              <div
                style={{ paddingBottom: '50px' }}
                className="p-4 bg-white border-b border-slate-200"
              >
                {children}
              </div>
            </div>
          </div>
        </main>
      </section>
    </div>
  );
}
