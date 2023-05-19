export default function Button({ type = 'submit', className = '', processing, children, ...rest }) {
  return (
    <button
      type={type}
      className={
        `inline-flex gap-1 items-center px-3 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-slate-600 transition ease-in-out duration-150 ${
          processing && 'opacity-25'
        } ` + className
      }
      disabled={processing}
      {...rest}
    >
      {rest.icon ?? ''}
      {children}
    </button>
  );
}
