import Button from '@/Components/Global/Button';
import { useForm } from '@inertiajs/inertia-react';
import toast from 'react-hot-toast';
import Modal from '@/Components/Global/Modal';
import { useState } from 'react';
import Input from '@/Components/Global/Input';
import UploadIcon from '@/Components/Icons/UploadIcon';

export default function FileImport({ processing }) {
  const [isOpenModal, setIsOpenModal] = useState(false);
  const { data, setData, post, reset } = useForm({
    file: '',
  });

  const handleFile = (e) => {
    setData('file', e.target.files[0]);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    post(route('tfn.import'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        e.target.reset();
        setIsOpenModal(false);
        toast.success('TFN file imported successfully.');
      },
    });
  };
  return (
    <>
      <Button className="mb-4" onClick={() => setIsOpenModal(true)} icon={<UploadIcon />}>
        Import TFN
      </Button>

      <Modal isOpen={isOpenModal} close={setIsOpenModal} title="Import New File">
        <form onSubmit={handleSubmit}>
          <div className="flex gap-4">
            <Input type="file" required onChange={handleFile} className="p-1 border w-full" />
            <Button type="submit" processing={processing}>
              Import
            </Button>
          </div>
        </form>
      </Modal>
    </>
  );
}
