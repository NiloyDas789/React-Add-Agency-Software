import CloseButton from '@/Components/Global/CloseButton';
import Heading from '@/Components/Global/Heading';

export default function Modal({ isOpen, close, title, children }) {
  return (
    <>
      <div
        className={
          (isOpen ? 'fixed' : 'hidden') +
          ' z-50 bg-white rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/3 top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2'
        }
      >
        <CloseButton className="absolute top-2 right-2" onClick={() => close(false)} />
        {title && (
          <div className="px-6 py-3 border-b">
            <Heading>{title}</Heading>
          </div>
        )}
        <div className="px-6 py-6 max-h-[80vh] overflow-auto">{children}</div>
      </div>
      <div
        className={(isOpen ? 'fixed' : 'hidden') + ' inset-0 z-20 bg-slate-800/50 backdrop-blur-sm'}
      ></div>
    </>
  );
}
