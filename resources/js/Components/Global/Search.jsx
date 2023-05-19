import useSearch from '@/Hooks/useSearch';
import React from 'react';

export default function Search({ searchInputEl, handleSearch, search }) {
  return (
    <div className="space-x-2 items-center mb-5 align-end justify-self-start md:ml-2 ">
      <input
        type="text"
        ref={searchInputEl}
        onChange={handleSearch}
        value={search}
        className="w-full pl-4 pr-4  rounded-lg border shadow-sm border-slate-300 focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-400 text-gray-600 font-medium"
        placeholder="Search..."
      />
    </div>
  );
}
