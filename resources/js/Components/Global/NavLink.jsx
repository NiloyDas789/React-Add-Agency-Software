import { Link } from '@inertiajs/inertia-react';

export default function NavLink({ href, active, children }) {
  return (
    <Link
      href={href}
      className={
        active
          ? 'flex items-center justify-between px-4 py-2.5 rounded-lg bg-slate-600 text-sm font-medium leading-5 text-white focus:outline-none focus:bg-slate-700 transition duration-150 ease-in-out'
          : 'flex items-center justify-between px-4 py-2.5 rounded-lg bg-transparent text-sm font-medium leading-5 text-slate-600 hover:text-slate-900 focus:outline-none focus:text-slate-900 transition duration-150 ease-in-out'
      }
    >
      {children}
    </Link>
  );
}
