import { Link } from '@inertiajs/inertia-react';

const PageLink = ({ active, label, url }) => {
  return (
    <Link
      className={`${
        active ? 'bg-slate-600 text-white' : 'bg-white'
      } mr-1 mb-1 px-4 py-2 border border-solid border-slate-300 rounded-md text-sm focus:outline-none focus:border-indigo-700 focus:text-indigo-700`}
      href={url}
    >
      <span dangerouslySetInnerHTML={{ __html: label }}></span>
    </Link>
  );
};

const PageInactive = ({ label }) => {
  return (
    <div
      className="mr-1 mb-1 px-4 py-2 text-sm border rounded-md border-solid border-slate-300 text-slate cursor-not-allowed"
      dangerouslySetInnerHTML={{ __html: label }}
    />
  );
};

export default function Pagination({ links = [] }) {
  if (links.length === 3) return null;

  return (
    <div className="flex flex-wrap mt-6 mb-1">
      {links.map(({ active, label, url }) => {
        return url === null ? (
          <PageInactive key={label} label={label} />
        ) : (
          <PageLink key={label} label={label} active={active} url={url} />
        );
      })}
    </div>
  );
}
