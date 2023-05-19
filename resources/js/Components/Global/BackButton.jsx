import React from 'react';
import LeftArrowIcon from '@/Components/Icons/LeftArrowIcon';
import { Link } from '@inertiajs/inertia-react';

export default function BackButton({ href }) {
  return (
    <Link
      href={href}
      className="cursor-pointer border border-transparent rounded-lg  text-xs text-slate-600 tracking-widest hover:bg-slate-200 hover:text-slate-600 transition ease-in-out duration-150"
    >
      <LeftArrowIcon />
    </Link>
  );
}
