import Button from '@/Components/Global/Button';
import Modal from '@/Components/Global/Modal';
import PlusIcon from '@/Components/Icons/PlusIcon';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Create() {
  const [isOpenModal, setIsOpenModal] = useState(false);
  const { data, setData, post, processing, errors, reset } = useForm({
    number: '',
    status: 1,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('tollFreeNumbers.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        setIsOpenModal(false);
        toast.success('TFN created successfully.');
      },
    });
  };

  return (
    <>
      <div className="flex gap-4 mb-4 items-center">
        <Button onClick={() => setIsOpenModal(true)} icon={<PlusIcon />}>
          Add TFN
        </Button>
      </div>
      <Modal isOpen={isOpenModal} close={setIsOpenModal} title="Create New TFNs">
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
        />
      </Modal>
    </>
  );
}
