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
    name: '',
    status: 1,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('states.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        setIsOpenModal(false);
        toast.success('State created successfully.');
      },
    });
  };

  return (
    <>
      <Button className="mb-4" onClick={() => setIsOpenModal(true)} icon={<PlusIcon />}>
        Add Restricted State
      </Button>
      <Modal isOpen={isOpenModal} close={setIsOpenModal} title="Create New State">
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
