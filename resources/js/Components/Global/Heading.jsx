export default function Heading({ className = '', children }) {
  return <h2 className={'font-semibold text-xl text-slate-700 ' + className}>{children}</h2>;
}
