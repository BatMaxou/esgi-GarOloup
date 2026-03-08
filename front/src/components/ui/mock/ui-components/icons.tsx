import { icons } from '@/components/ui/atoms/Icon/config';
import Icon from '@/components/ui/atoms/Icon';
import Typography from '@/components/ui/atoms/typography';

const Icons = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Icons
      </Typography>
      <div className="flex flex-wrap gap-8">
        <ul className="bg-neutral-100 rounded-sm p-8 flex flex-wrap gap-8 shadow-(--shadow)">
          {Object.keys(icons).map((iconName) => (
            <li key={iconName}>
              <Icon name={iconName} title={iconName} className="w-16 h-16" />
            </li>
          ))}
        </ul>
      </div>
    </>
  );
};

export default Icons;
