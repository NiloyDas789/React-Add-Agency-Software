import { useState } from 'react';
import DownloadIcon from '@/Components/Icons/DownloadIcon';
import Button from '@/Components/Global/Button';
import Modal from '@/Components/Global/Modal';

export default function CsvDownload({ href, children, processing }) {
  const [isOpenModal, setIsOpenModal] = useState(false);

  return (
    <>
      <Button onClick={() => setIsOpenModal(true)} className="mb-6" icon={<DownloadIcon />}>
        {children}
      </Button>

      <Modal isOpen={isOpenModal} close={setIsOpenModal}>
        <h2 className="font-semibold text-xl text-center mb-6">Are you sure?</h2>
        <div className="flex justify-center gap-4">
          <Button
            onClick={() => setIsOpenModal(false)}
            className="bg-slate-700 text-white active:bg-slate-300"
          >
            Cancel
          </Button>

          <a
            href={href}
            target="blank"
            disabled={processing}
            onClick={() => setIsOpenModal(false)}
            className="inline-flex gap-1 items-center px-3 py-2 bg-purple-800 font-semibold text-xs text-white uppercase rounded-md active:bg-purple-900"
          >
            Download
          </a>
        </div>
      </Modal>
    </>
  );
}
