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
    delivery_method: '',
    response_type: '',
    timezone: '',
    delivery_days: '',
    auto_delivery: '',
    file_naming_convention: '',
    contact_name: '',
    contact_email: '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('providers.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        setIsOpenModal(false);
        toast.success('Provider created successfully.');
      },
    });
  };

  return (
    <>
      <Button onClick={() => setIsOpenModal(true)} className="mb-6" icon={<PlusIcon />}>
        Add Provider
      </Button>
      <Modal isOpen={isOpenModal} close={setIsOpenModal} title="Create New Provider">
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
