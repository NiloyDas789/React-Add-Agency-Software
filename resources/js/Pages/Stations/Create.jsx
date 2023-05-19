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
    title: '',
    status: 1,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('stations.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        setIsOpenModal(false);
        toast.success('Station created successfully.');
      },
    });
  };

  return (
    <>
      <Button className="mb-4" onClick={() => setIsOpenModal(true)} icon={<PlusIcon />}>
        Add Station
      </Button>
      <Modal isOpen={isOpenModal} close={setIsOpenModal} title="Create New Station">
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
